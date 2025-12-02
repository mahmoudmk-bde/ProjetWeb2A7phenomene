<?php

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/StoreItem.php';

class StoreController {
    private $db;
    private $storeItem;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
        $this->storeItem = new StoreItem($this->db);
    }

    // Afficher le store (tous les jeux)
    public function index() {
        $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;
        $stmt = $this->storeItem->getAll();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($categorie) {
            $canon = strtolower(trim($categorie));
            $items = array_values(array_filter($items, function($item) use ($canon) {
                $val = strtolower(trim($item['categorie'] ?? ''));
                $val = strtr($val, [
                    'é' => 'e','è'=>'e','ê'=>'e','ë'=>'e','à'=>'a','â'=>'a','î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ç'=>'c'
                ]);
                $canonN = strtr($canon, [
                    'é' => 'e','è'=>'e','ê'=>'e','ë'=>'e','à'=>'a','â'=>'a','î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ç'=>'c'
                ]);
                if ($canonN === 'education') { $canonN = 'educatif'; }
                if ($val === 'éducatif') { $val = 'educatif'; }
                return $val === $canonN;
            }));
        }
        
        include __DIR__ . '/../view/frontoffice/store/index.php';
    }

    // detail jeu
    public function show() {
        if (isset($_GET['id'])) {
            $this->storeItem->id = $_GET['id'];
            
            if ($this->storeItem->getById()) {
                if (!isset($_SESSION['viewed_items'])) {
                    $_SESSION['viewed_items'] = [];
                }
                $viewKey = (string)$this->storeItem->id;
                if (!isset($_SESSION['viewed_items'][$viewKey])) {
                    $_SESSION['viewed_items'][$viewKey] = time();
                    try {
                        $stmtInc = $this->db->prepare("UPDATE store_items SET views_count = COALESCE(views_count,0) + 1 WHERE id = :id");
                        $stmtInc->bindParam(':id', $this->storeItem->id);
                        $stmtInc->execute();
                    } catch (PDOException $e) {}
                }
                // recup jeux du meme partenaire
                $stmt = $this->storeItem->getByPartenaire($this->storeItem->partenaire_id);
                $autresJeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Exclure le jeu actuel
                $autresJeux = array_filter($autresJeux, function($item) {
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
                } catch (PDOException $e) {}
                // Lecture rating agrégé via colonnes si disponibles
                try {
                    $stmtRA = $this->db->prepare("SELECT rating_avg, rating_count FROM store_items WHERE id = :id");
                    $stmtRA->bindParam(':id', $this->storeItem->id);
                    $stmtRA->execute();
                    $rr = $stmtRA->fetch(PDO::FETCH_ASSOC);
                    if ($rr && $rr['rating_count'] !== null) {
                        $itemRatingAvg = (float)$rr['rating_avg'];
                        $itemRatingCount = (int)$rr['rating_count'];
                    }
                } catch (PDOException $eA) {}
                try {
                    $stmtR = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM item_ratings WHERE store_item_id = :id");
                    $stmtR->bindParam(':id', $this->storeItem->id);
                    $stmtR->execute();
                    $r = $stmtR->fetch(PDO::FETCH_ASSOC);
                    if ($r && $itemRatingCount === 0) { $itemRatingAvg = (float)$r['avg_score']; $itemRatingCount = (int)$r['cnt']; }
                } catch (PDOException $e1) {
                    try {
                        $stmtRF = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM store_item_ratings WHERE store_item_id = :id");
                        $stmtRF->bindParam(':id', $this->storeItem->id);
                        $stmtRF->execute();
                        $r = $stmtRF->fetch(PDO::FETCH_ASSOC);
                        if ($r && $itemRatingCount === 0) { $itemRatingAvg = (float)$r['avg_score']; $itemRatingCount = (int)$r['cnt']; }
                    } catch (PDOException $e2) {}
                }
                include __DIR__ . '/../view/frontoffice/store/item-detail.php';
            } else {
                header("Location: ?controller=Store&action=index");
                exit;
            }
        }
    }

    public function rateItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int)$_GET['id'];
        $score = isset($_POST['score']) ? (int)$_POST['score'] : 0;
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
                } catch (PDOException $e2) {}
            }
        }
        header("Location: ?controller=Store&action=show&id=" . $id);
    }

    public function addToCart() {
        if (!isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int)$_GET['id'];
        $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->storeItem->id = $id;
        if ($this->storeItem->getById()) {
            $current = $_SESSION['cart'][$id] ?? 0;
            $newQty = min($current + $qty, (int)$this->storeItem->stock);
            if ($newQty > 0) {
                $_SESSION['cart'][$id] = $newQty;
            }
        }
        // Retourner à la page précédente au lieu du panier
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?controller=Store&action=index';
        header("Location: " . $referer . (strpos($referer, '?') !== false ? '&' : '?') . "added=1");
    }

    public function cart() {
        $items = [];
        $total = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $qty) {
                $si = new StoreItem($this->db);
                $si->id = (int)$id;
                if ($si->getById()) {
                    $lineTotal = (float)$si->prix * (int)$qty;
                    $items[] = [
                        'id' => $si->id,
                        'nom' => $si->nom,
                        'image' => $si->image,
                        'prix' => $si->prix,
                        'stock' => $si->stock,
                        'qty' => (int)$qty,
                        'line_total' => $lineTotal
                    ];
                    $total += $lineTotal;
                }
            }
        }
        include __DIR__ . '/../view/frontoffice/store/cart.php';
    }

    public function updateCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $id => $qty) {
                $idInt = (int)$id;
                $qtyInt = max(0, (int)$qty);
                if ($qtyInt === 0) {
                    unset($_SESSION['cart'][$idInt]);
                } else {
                    $si = new StoreItem($this->db);
                    $si->id = $idInt;
                    if ($si->getById()) {
                        $_SESSION['cart'][$idInt] = min($qtyInt, (int)$si->stock);
                    }
                }
            }
        }
        header("Location: ?controller=Store&action=cart&updated=1");
    }

    public function removeFromCart() {
        if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
            unset($_SESSION['cart'][$_GET['id']]);
        }
        header("Location: ?controller=Store&action=cart&removed=1");
    }

    public function toggleLike() {
        if (!isset($_GET['id'])) {
            header("Location: ?controller=Store&action=index");
            return;
        }
        $id = (int)$_GET['id'];
        if (!isset($_SESSION['liked_items'])) {
            $_SESSION['liked_items'] = [];
        }
        $liked = isset($_SESSION['liked_items'][$id]);
        try {
            if ($liked) {
                $stmt = $this->db->prepare("UPDATE store_items SET likes_count = GREATEST(COALESCE(likes_count,0)-1,0) WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                unset($_SESSION['liked_items'][$id]);
            } else {
                $stmt = $this->db->prepare("UPDATE store_items SET likes_count = COALESCE(likes_count,0) + 1 WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $_SESSION['liked_items'][$id] = true;
            }
        } catch (PDOException $e) {}
        header("Location: ?controller=Store&action=show&id=" . $id);
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $author = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            if ($content !== '') {
                try {
                    $stmt = $this->db->prepare("INSERT INTO item_comments (store_item_id, author_name, content, status, created_at) VALUES (:id, :author, :content, 'approved', NOW())");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':author', $author);
                    $stmt->bindParam(':content', $content);
                    $stmt->execute();
                } catch (PDOException $e) {}
            }
            header("Location: ?controller=Store&action=show&id=" . $id);
            return;
        }
        header("Location: ?controller=Store&action=index");
    }

    public function clearCart() {
        $_SESSION['cart'] = [];
        header("Location: ?controller=Store&action=cart&cleared=1");
    }

    public function checkout() {
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
        if (strlen($name) < 3) { $errors[] = "Nom invalide (minimum 3 caractères)"; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Email invalide"; }
        if (strlen(preg_replace('/\\D/', '', $phone)) < 8) { $errors[] = "Téléphone invalide (minimum 8 chiffres)"; }
        if (strlen($address) < 5) { $errors[] = "Adresse invalide (minimum 5 caractères)"; }
        if (strlen($city) < 2) { $errors[] = "Ville invalide (minimum 2 caractères)"; }
        if ($shipping === '') { $errors[] = "Mode de livraison non sélectionné"; }
        
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
            $si->id = (int)$id;
            if ($si->getById()) {
                $qtyInt = (int)$qty;
                $lineTotal = (float)$si->prix * $qtyInt;
                $items[] = [
                    'id' => $si->id,
                    'nom' => $si->nom,
                    'prix' => (float)$si->prix,
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
        
        $userId = (int)$_SESSION['user_id'];
        
        try {
            $this->db->beginTransaction();
            
            $stmtOrder = $this->db->prepare("INSERT INTO orders (utilisateur_id, name, email, phone, address, city, shipping, total, created_at) VALUES (:utilisateur_id, :name, :email, :phone, :address, :city, :shipping, :total, NOW())");
            $stmtOrder->bindParam(':utilisateur_id', $userId);
            $stmtOrder->bindParam(':name', $name);
            $stmtOrder->bindParam(':email', $email);
            $stmtOrder->bindParam(':phone', $phone);
            $stmtOrder->bindParam(':address', $address);
            $stmtOrder->bindParam(':city', $city);
            $stmtOrder->bindParam(':shipping', $shipping);
            $stmtOrder->bindParam(':total', $total);
            $stmtOrder->execute();
            $orderId = (int)$this->db->lastInsertId();
            $itemsSql = "INSERT INTO order_items (order_id, item_id, name, price, qty) VALUES (:order_id, :item_id, :name, :price, :qty)";
            $stmtItem = $this->db->prepare($itemsSql);
            try {
                foreach ($items as $it) {
                    $stmtItem->bindParam(':order_id', $orderId);
                    $stmtItem->bindParam(':item_id', $it['id']);
                    $stmtItem->bindParam(':name', $it['nom']);
                    $stmtItem->bindParam(':price', $it['prix']);
                    $stmtItem->bindParam(':qty', $it['qty']);
                    $stmtItem->execute();
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
}
?>
