<?php

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/StoreItem.php';

class StoreController
{
    private $db;
    private $storeItem;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
        $this->storeItem = new StoreItem($this->db);
    }

    // Afficher le store (tous les jeux)
    public function index()
    {
        $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;
        $stmt = $this->storeItem->getAll();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($categorie) {
            $canon = strtolower(trim($categorie));
            $items = array_values(array_filter($items, function ($item) use ($canon) {
                $val = strtolower(trim($item['categorie'] ?? ''));
                $val = strtr($val, [
                    'é' => 'e',
                    'è' => 'e',
                    'ê' => 'e',
                    'ë' => 'e',
                    'à' => 'a',
                    'â' => 'a',
                    'î' => 'i',
                    'ï' => 'i',
                    'ô' => 'o',
                    'ö' => 'o',
                    'ù' => 'u',
                    'û' => 'u',
                    'ç' => 'c'
                ]);
                $canonN = strtr($canon, [
                    'é' => 'e',
                    'è' => 'e',
                    'ê' => 'e',
                    'ë' => 'e',
                    'à' => 'a',
                    'â' => 'a',
                    'î' => 'i',
                    'ï' => 'i',
                    'ô' => 'o',
                    'ö' => 'o',
                    'ù' => 'u',
                    'û' => 'u',
                    'ç' => 'c'
                ]);
                if ($canonN === 'education') {
                    $canonN = 'educatif';
                }
                if ($val === 'éducatif') {
                    $val = 'educatif';
                }
                return $val === $canonN;
            }));
        }

        // Pagination
        $limit = 6;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $totalItems = count($items);
        $totalPages = ceil($totalItems / $limit);

        // Ensure page is valid
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        // Slice items for current page
        $items = array_slice($items, ($page - 1) * $limit, $limit);

        include __DIR__ . '/../view/frontoffice/store/index.php';
    }

    // detail jeu
    public function show()
    {
        if (isset($_GET['id'])) {
            $this->storeItem->id = $_GET['id'];

            if ($this->storeItem->getById()) {
                if (!isset($_SESSION['viewed_items'])) {
                    $_SESSION['viewed_items'] = [];
                }
                $viewKey = (string) $this->storeItem->id;
                if (!isset($_SESSION['viewed_items'][$viewKey])) {
                    $_SESSION['viewed_items'][$viewKey] = time();
                    try {
                        $stmtInc = $this->db->prepare("UPDATE store_items SET views_count = COALESCE(views_count,0) + 1 WHERE id = :id");
                        $stmtInc->bindParam(':id', $this->storeItem->id);
                        $stmtInc->execute();
                    } catch (PDOException $e) {
                    }
                }
                // recup jeux du meme partenaire
                $stmt = $this->storeItem->getByPartenaire($this->storeItem->partenaire_id);
                $autresJeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Exclure le jeu actuel
                $autresJeux = array_filter($autresJeux, function ($item) {
                    return $item['id'] != $this->storeItem->id;
                });
                $comments = [];
                $itemRatingAvg = null;
                $itemRatingCount = 0;
                try {
                    $stmtC = $this->db->prepare("SELECT id, author_name, content, created_at FROM item_comments WHERE store_item_id = :id AND status = 'approved' ORDER BY created_at DESC");
                    $stmtC->bindParam(':id', $this->storeItem->id);
                    $stmtC->execute();
                    $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                }
                // Lecture rating agrégé via colonnes si disponibles
                try {
                    $stmtRA = $this->db->prepare("SELECT rating_avg, rating_count FROM store_items WHERE id = :id");
                    $stmtRA->bindParam(':id', $this->storeItem->id);
                    $stmtRA->execute();
                    $rr = $stmtRA->fetch(PDO::FETCH_ASSOC);
                    if ($rr && $rr['rating_count'] !== null) {
                        $itemRatingAvg = (float) $rr['rating_avg'];
                        $itemRatingCount = (int) $rr['rating_count'];
                    }
                } catch (PDOException $eA) {
                }
                try {
                    $stmtR = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM item_ratings WHERE store_item_id = :id");
                    $stmtR->bindParam(':id', $this->storeItem->id);
                    $stmtR->execute();
                    $r = $stmtR->fetch(PDO::FETCH_ASSOC);
                    if ($r && $itemRatingCount === 0) {
                        $itemRatingAvg = (float) $r['avg_score'];
                        $itemRatingCount = (int) $r['cnt'];
                    }
                } catch (PDOException $e1) {
                    try {
                        $stmtRF = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM store_item_ratings WHERE store_item_id = :id");
                        $stmtRF->bindParam(':id', $this->storeItem->id);
                        $stmtRF->execute();
                        $r = $stmtRF->fetch(PDO::FETCH_ASSOC);
                        if ($r && $itemRatingCount === 0) {
                            $itemRatingAvg = (float) $r['avg_score'];
                            $itemRatingCount = (int) $r['cnt'];
                        }
                    } catch (PDOException $e2) {
                    }
                }
                include __DIR__ . '/../view/frontoffice/store/item-detail.php';
            } else {
                header("Location: ?controller=Store&action=index");
                exit;
            }
        }
    }

    public function rateItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int) $_GET['id'];
        $score = isset($_POST['score']) ? (int) $_POST['score'] : 0;
        $author = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
        if ($score < 1 || $score > 5) {
            header("Location: ?controller=Store&action=show&id=" . $id);
            return;
        }
        // Essai sans nouvelle table: colonnes agrégées sur store_items
        try {
            $stmtU = $this->db->prepare("UPDATE store_items SET rating_avg = (COALESCE(rating_sum,0)+:score)/(COALESCE(rating_count,0)+1), rating_sum = COALESCE(rating_sum,0)+:score, rating_count = COALESCE(rating_count,0)+1 WHERE id = :id");
            $stmtU->bindParam(':score', $score);
            $stmtU->bindParam(':id', $id);
            $stmtU->execute();
        } catch (PDOException $eU) {
            // Fallback: insertion dans une table de ratings si colonnes absentes
            try {
                $stmt = $this->db->prepare("INSERT INTO item_ratings (store_item_id, author_name, score, created_at) VALUES (:id, :author, :score, NOW())");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':author', $author);
                $stmt->bindParam(':score', $score);
                $stmt->execute();
            } catch (PDOException $e) {
                try {
                    $stmtF = $this->db->prepare("INSERT INTO store_item_ratings (store_item_id, author_name, score, created_at) VALUES (:id, :author, :score, NOW())");
                    $stmtF->bindParam(':id', $id);
                    $stmtF->bindParam(':author', $author);
                    $stmtF->bindParam(':score', $score);
                    $stmtF->execute();
                } catch (PDOException $e2) {
                }
            }
        }
        header("Location: ?controller=Store&action=show&id=" . $id);
    }

    public function addToCart()
    {
        if (!isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int) $_GET['id'];
        $qty = isset($_POST['qty']) ? max(1, (int) $_POST['qty']) : 1;
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->storeItem->id = $id;
        if ($this->storeItem->getById()) {
            $current = $_SESSION['cart'][$id] ?? 0;
            $newQty = min($current + $qty, (int) $this->storeItem->stock);
            if ($newQty > 0) {
                $_SESSION['cart'][$id] = $newQty;
            }
        }
        // Retourner à la page précédente au lieu du panier
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?controller=Store&action=index';
        header("Location: " . $referer . (strpos($referer, '?') !== false ? '&' : '?') . "added=1");
    }

    public function cart()
    {
        $items = [];
        $total = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $qty) {
                $si = new StoreItem($this->db);
                $si->id = (int) $id;
                if ($si->getById()) {
                    $lineTotal = (float) $si->prix * (int) $qty;
                    $items[] = [
                        'id' => $si->id,
                        'nom' => $si->nom,
                        'image' => $si->image,
                        'prix' => $si->prix,
                        'stock' => $si->stock,
                        'qty' => (int) $qty,
                        'line_total' => $lineTotal
                    ];
                    $total += $lineTotal;
                }
            }
        }
        include __DIR__ . '/../view/frontoffice/store/cart.php';
    }

    public function updateCart()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $id => $qty) {
                $idInt = (int) $id;
                $qtyInt = max(0, (int) $qty);
                if ($qtyInt === 0) {
                    unset($_SESSION['cart'][$idInt]);
                } else {
                    $si = new StoreItem($this->db);
                    $si->id = $idInt;
                    if ($si->getById()) {
                        $_SESSION['cart'][$idInt] = min($qtyInt, (int) $si->stock);
                    }
                }
            }
        }
        header("Location: ?controller=Store&action=cart&updated=1");
    }

    public function removeFromCart()
    {
        if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
            unset($_SESSION['cart'][$_GET['id']]);
        }
        header("Location: ?controller=Store&action=cart&removed=1");
    }

    public function wishlist()
    {
        $items = [];
        if (isset($_SESSION['liked_items']) && !empty($_SESSION['liked_items'])) {
            // Re-fetch items to ensure they exist and get latest data
            foreach ($_SESSION['liked_items'] as $id => $val) {
                $si = new StoreItem($this->db);
                $si->id = (int) $id;
                if ($si->getById()) {
                    $items[] = [
                        'id' => $si->id,
                        'nom' => $si->nom,
                        'image' => $si->image,
                        'prix' => $si->prix,
                        'categorie' => $si->categorie,
                        'stock' => $si->stock
                    ];
                }
            }
        }
        $isWishlist = true;
        // Reuse index view or specific wishlist view? The plan said wishlist.php
        include __DIR__ . '/../view/frontoffice/store/wishlist.php';
    }

    public function toggleLike()
    {
        if (!isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int) $_GET['id'];
        if (!isset($_SESSION['liked_items'])) {
            $_SESSION['liked_items'] = [];
        }
        $liked = isset($_SESSION['liked_items'][$id]);
        try {
            if ($liked) {
                // Try to update DB, but don't block session update
                $stmt = $this->db->prepare("UPDATE store_items SET likes_count = GREATEST(COALESCE(likes_count,0)-1,0) WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE store_items SET likes_count = COALESCE(likes_count,0) + 1 WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            // Log error but continue
        }

        // Update session state regardless of DB success
        if ($liked) {
            unset($_SESSION['liked_items'][$id]);
        } else {
            $_SESSION['liked_items'][$id] = true;
        }

        // Redirect back to whence we came
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?controller=Store&action=show&id=' . $id;
        header("Location: " . $referer);
    }

    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $author = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            if ($content !== '') {
                try {
                    $stmt = $this->db->prepare("INSERT INTO item_comments (store_item_id, author_name, content, status, created_at) VALUES (:id, :author, :content, 'approved', NOW())");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':author', $author);
                    $stmt->bindParam(':content', $content);
                    $stmt->execute();
                } catch (PDOException $e) {
                }
            }
            header("Location: ?controller=Store&action=show&id=" . $id);
            return;
        }
        header("Location: ?controller=Store&action=index");
    }

    public function clearCart()
    {
        $_SESSION['cart'] = [];
        header("Location: ?controller=Store&action=cart&cleared=1");
    }

    public function checkout()
    {
        // Debug: Log session info
        error_log("Checkout attempt - Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'));
        error_log("POST data: " . print_r($_POST, true));

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $errorMsg = "Panier vide";
            $_SESSION['checkout_error'] = $errorMsg;
            header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
            return;
        }
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $shipping = isset($_POST['shipping']) ? trim($_POST['shipping']) : '';

        $errors = [];
        if (strlen($name) < 3) {
            $errors[] = "Nom invalide (minimum 3 caractères)";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }
        if (strlen(preg_replace('/\\D/', '', $phone)) < 8) {
            $errors[] = "Téléphone invalide (minimum 8 chiffres)";
        }
        if (strlen($address) < 5) {
            $errors[] = "Adresse invalide (minimum 5 caractères)";
        }
        if (strlen($city) < 2) {
            $errors[] = "Ville invalide (minimum 2 caractères)";
        }
        if ($shipping === '') {
            $errors[] = "Mode de livraison non sélectionné";
        }

        if (!empty($errors)) {
            $errorMsg = implode(', ', $errors);
            error_log("Validation errors: " . $errorMsg);
            $_SESSION['checkout_error'] = $errorMsg;
            header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
            return;
        }
        $total = 0;
        $items = [];
        foreach ($_SESSION['cart'] as $id => $qty) {
            $si = new StoreItem($this->db);
            $si->id = (int) $id;
            if ($si->getById()) {
                $qtyInt = (int) $qty;
                $lineTotal = (float) $si->prix * $qtyInt;
                $items[] = [
                    'id' => $si->id,
                    'nom' => $si->nom,
                    'prix' => (float) $si->prix,
                    'qty' => $qtyInt
                ];
                $total += $lineTotal;
            }
        }

        // Check if user is logged in (required for checkout)
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $errorMsg = "Vous devez être connecté pour passer une commande";
            $_SESSION['checkout_error'] = $errorMsg;
            header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
            return;
        }

        $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'onsite';

        // --- PROCESS PAYMENT ---
        // --- STOCK VALIDATION ---
        foreach ($items as $it) {
            $stmtStock = $this->db->prepare("SELECT stock, nom FROM store_items WHERE id = :id");
            $stmtStock->execute([':id' => $it['id']]);
            $stockData = $stmtStock->fetch(PDO::FETCH_ASSOC);

            if (!$stockData || $stockData['stock'] < $it['qty']) {
                $errorMsg = "Stock insuffisant pour l'article : " . ($stockData['nom'] ?? 'Inconnu');
                header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
                return;
            }
        }
        // --- END STOCK VALIDATION ---

        // --- PROCESS PAYMENT ---
        if ($paymentMethod === 'online') {
            require_once __DIR__ . '/../view/frontoffice/vender/stripe-php-19.0.0/stripe-php-19.0.0/init.php';

            // API Keys (Test Keys)
            // Load .env if not using a library
            if (file_exists(__DIR__ . '/../../.env')) {
                $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0)
                        continue;
                    list($name, $value) = explode('=', $line, 2);
                    $_ENV[trim($name)] = trim($value);
                }
            }

            $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            if (!$stripeSecretKey) {
                // Fallback or Error handling
                $stripeSecretKey = 'SIMULATION_MODE';
            }
            \Stripe\Stripe::setApiKey($stripeSecretKey);

            $cardNumber = isset($_POST['card_number']) ? str_replace(' ', '', $_POST['card_number']) : '';
            $cardExpiry = isset($_POST['card_expiry']) ? explode('/', $_POST['card_expiry']) : [];
            $cardCvc = isset($_POST['card_cvc']) ? $_POST['card_cvc'] : '';

            $expMonth = isset($cardExpiry[0]) ? trim($cardExpiry[0]) : '';
            $expYear = isset($cardExpiry[1]) ? trim($cardExpiry[1]) : '';
            // Handle 2 digit year (assume 20xx)
            if (strlen($expYear) == 2)
                $expYear = '20' . $expYear;

            $isSimulation = ($stripeSecretKey === 'SIMULATION_MODE');

            if (!$isSimulation) {
                try {
                    // 1. Create Token
                    $token = \Stripe\Token::create([
                        'card' => [
                            'number' => $cardNumber,
                            'exp_month' => $expMonth,
                            'exp_year' => $expYear,
                            'cvc' => $cardCvc,
                        ],
                    ]);

                    // 2. Create Charge
                    $charge = \Stripe\Charge::create([
                        'amount' => (int) ($total * 100 * 3.3),
                        'currency' => 'usd',
                        'description' => 'Commande Engage Store - ' . $email,
                        'source' => $token->id,
                    ]);

                } catch (\Exception $e) {
                    $errorMsg = "Erreur paiement: " . $e->getMessage();
                    $_SESSION['checkout_error'] = $errorMsg;
                    header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
                    return;
                }
            } else {
                // SIMULATION MODE: Simulate 1 second delay
                sleep(1);
            }
        }
        // --- END PAYMENT ---

        // Create Order in DB
        $userId = (int) $_SESSION['user_id'];

        try {
            $this->db->beginTransaction();

            $stmtOrder = $this->db->prepare("INSERT INTO orders (utilisateur_id, name, email, phone, address, city, shipping, total, payment_method, created_at) VALUES (:utilisateur_id, :name, :email, :phone, :address, :city, :shipping, :total, :payment_method, NOW())");
            $stmtOrder->bindParam(':utilisateur_id', $userId);
            $stmtOrder->bindParam(':name', $name);
            $stmtOrder->bindParam(':email', $email);
            $stmtOrder->bindParam(':phone', $phone);
            $stmtOrder->bindParam(':address', $address);
            $stmtOrder->bindParam(':city', $city);
            $stmtOrder->bindParam(':shipping', $shipping);
            $stmtOrder->bindParam(':payment_method', $paymentMethod);

            $stmtOrder->bindParam(':total', $total);
            $stmtOrder->execute();
            $orderId = (int) $this->db->lastInsertId();

            $itemsSql = "INSERT INTO order_items (order_id, item_id, name, price, qty) VALUES (:order_id, :item_id, :name, :price, :qty)";
            $stmtItem = $this->db->prepare($itemsSql);

            // Prepare Stock Update Statement
            $stmtUpdateStock = $this->db->prepare("UPDATE store_items SET stock = stock - :qty WHERE id = :id");

            try {
                foreach ($items as $it) {
                    $stmtItem->bindParam(':order_id', $orderId);
                    $stmtItem->bindParam(':item_id', $it['id']);
                    $stmtItem->bindParam(':name', $it['nom']);
                    $stmtItem->bindParam(':price', $it['prix']);
                    $stmtItem->bindParam(':qty', $it['qty']);
                    $stmtItem->execute();

                    // Decrement Stock
                    $stmtUpdateStock->execute([':qty' => $it['qty'], ':id' => $it['id']]);
                }
            } catch (PDOException $eItems) {
                $fallbackSql = "INSERT INTO oder_items (order_id, item_id, name, price, qty) VALUES (:order_id, :item_id, :name, :price, :qty)";
                $stmtItemFallback = $this->db->prepare($fallbackSql);
                foreach ($items as $it) {
                    $stmtItemFallback->bindParam(':order_id', $orderId);
                    $stmtItemFallback->bindParam(':item_id', $it['id']);
                    $stmtItemFallback->bindParam(':name', $it['nom']);
                    $stmtItemFallback->bindParam(':price', $it['prix']);
                    $stmtItemFallback->bindParam(':qty', $it['qty']);
                    $stmtItemFallback->execute();

                    // Decrement Stock (Also here just in case)
                    $stmtUpdateStock->execute([':qty' => $it['qty'], ':id' => $it['id']]);
                }
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            $errorMsg = "Erreur de base de données: " . $e->getMessage();
            $_SESSION['checkout_error'] = $errorMsg;
            header("Location: ?controller=Store&action=cart&order=invalid&error=" . urlencode($errorMsg));
            return;
        }
        $_SESSION['cart'] = [];
        header("Location: ?controller=Store&action=cart&order=success");
    }
    public function getUserOrders($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE utilisateur_id = :uid ORDER BY created_at DESC");
            $stmt->bindParam(':uid', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getOrderItems($orderId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :oid");
            $stmt->bindParam(':oid', $orderId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM oder_items WHERE order_id = :oid");
                $stmt->bindParam(':oid', $orderId);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                return [];
            }
        }
    }

    // --- AI RECOMMENDATION SYSTEM ---
    public function recommendations()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: connexion.php');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $recommendations = $this->getRecommendations($userId);

        include __DIR__ . '/../view/frontoffice/store/recommendations.php';
    }

    private function getRecommendations($userId)
    {
        // 1. Fetch User Preferences (Categories from Orders & Wishlist)
        $preferences = [];

        // From Orders
        $stmtOrders = $this->db->prepare("
            SELECT DISTINCT si.categorie 
            FROM store_items si
            JOIN order_items oi ON si.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.utilisateur_id = :uid
        ");
        $stmtOrders->execute([':uid' => $userId]);
        $boughtCategories = $stmtOrders->fetchAll(PDO::FETCH_COLUMN);

        // From Wishlist
        $stmtWish = $this->db->prepare("
            SELECT DISTINCT si.categorie 
            FROM store_items si
            JOIN wishlist w ON si.id = w.store_item_id
            WHERE w.user_id = :uid
        ");
        $stmtWish->execute([':uid' => $userId]);
        $wishCategories = $stmtWish->fetchAll(PDO::FETCH_COLUMN);

        // 2. Fetch All Candidate Items (Not owned, Not in wishlist)
        // We exclude items the user already has strictly to suggest NEW things
        $sqlCandidates = "
            SELECT * FROM store_items 
            WHERE id NOT IN (
                SELECT product_id FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.utilisateur_id = :uid1
            )
            AND id NOT IN (
                SELECT store_item_id FROM wishlist WHERE user_id = :uid2
            )
        ";
        $stmtCand = $this->db->prepare($sqlCandidates);
        $stmtCand->execute([':uid1' => $userId, ':uid2' => $userId]);
        $candidates = $stmtCand->fetchAll(PDO::FETCH_ASSOC);

        // 3. Score Candidates
        $scoredItems = [];
        foreach ($candidates as $item) {
            $score = 0;

            // +10 points if category matches purchase history
            if (in_array($item['categorie'], $boughtCategories)) {
                $score += 10;
            }

            // +5 points if category matches wishlist
            if (in_array($item['categorie'], $wishCategories)) {
                $score += 5;
            }

            // Bonus: New items get a small boost (+1)
            // (Optional, keeps content fresh)
            $score += 1;

            if ($score > 1) { // Only recommend if there's some relevance
                $item['match_score'] = $score;
                $scoredItems[] = $item;
            }
        }

        // 4. Sort by Score Descending
        usort($scoredItems, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        // Return top 6
        return array_slice($scoredItems, 0, 6);
    }
}

?>