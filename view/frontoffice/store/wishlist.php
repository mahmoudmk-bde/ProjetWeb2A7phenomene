<?php
// $items contains liked items from StoreController::wishlist()
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ma Liste d'Envies - Engage Store</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        .wishlist-section {
            padding: 80px 0;
            min-height: 60vh;
        }

        .wishlist-card {
            background: var(--panel);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .wishlist-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }

        .wishlist-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .wishlist-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 200px);
        }

        .wishlist-title {
            color: white;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .wishlist-price {
            color: var(--accent);
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .wishlist-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        .btn-remove-wish {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-remove-wish:hover {
            background: var(--danger);
        }
    </style>
</head>

<body>
    <div class="body_bg">
        <?php include __DIR__ . '/../header_common1.php'; ?>

        <section class="breadcrumb breadcrumb_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb_iner text-center">
                            <div class="breadcrumb_iner_item">
                                <h2>Ma Liste d'Envies</h2>
                                <p><a href="?controller=Store&action=index">Store</a> / Wishlist</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="wishlist-section">
            <div class="container">
                <?php if (empty($items)): ?>
                    <div class="text-center">
                        <i class="far fa-heart fa-4x mb-4" style="color: var(--muted);"></i>
                        <h3 class="text-white">Votre liste d'envies est vide</h3>
                        <p class="text-muted mb-4">Sauvegardez vos jeux préférés pour plus tard</p>
                        <a href="?controller=Store&action=index" class="btn_1">Parcourir le Store</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($items as $item): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="wishlist-card">
                                    <a href="?controller=Store&action=show&id=<?= $item['id'] ?>">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo BASE_URL . $item['image']; ?>" alt="" class="wishlist-img">
                                        <?php else: ?>
                                            <div class="wishlist-img d-flex align-items-center justify-content-center bg-dark">
                                                <i class="fas fa-gamepad fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="wishlist-body">
                                        <a href="?controller=Store&action=show&id=<?= $item['id'] ?>">
                                            <h5 class="wishlist-title"><?= htmlspecialchars($item['nom']) ?></h5>
                                        </a>
                                        <div class="wishlist-price"><?= number_format($item['prix'], 2) ?> DT</div>

                                        <div class="wishlist-actions">
                                            <form action="?controller=Store&action=addToCart&id=<?= $item['id'] ?>"
                                                method="post" style="flex:1;">
                                                <button type="submit" class="btn_1 w-100" style="padding: 10px;">Au
                                                    panier</button>
                                            </form>
                                            <a href="?controller=Store&action=toggleLike&id=<?= $item['id'] ?>"
                                                class="btn-remove-wish" title="Retirer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="footer_part">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="copyright_text text-center">
                            <p class="footer-text m-0">©
                                <script>document.write(new Date().getFullYear());</script> Engage. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/custom.js"></script>
</body>

</html>