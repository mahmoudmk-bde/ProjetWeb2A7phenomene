<?php
// $items is passed from StoreController::cart()
$total = 0;
foreach ($items as $item) {
    $total += $item['line_total'];
}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mon Panier - Engage Store</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        .cart-section {
            padding: 80px 0;
            min-height: 60vh;
        }

        .cart-table {
            background: var(--panel);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .cart-table th {
            background: rgba(255, 255, 255, 0.05);
            color: var(--muted);
            border: none;
            padding: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .cart-table td {
            padding: 20px;
            vertical-align: middle;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: white;
        }

        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
        }

        .qty-input {
            width: 60px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 5px;
            padding: 5px;
            text-align: center;
        }

        .btn-remove {
            color: var(--danger);
            background: rgba(220, 53, 69, 0.1);
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .btn-remove:hover {
            background: var(--danger);
            color: white;
        }

        .cart-summary {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--muted);
        }

        .summary-row.total {
            color: white;
            font-weight: bold;
            font-size: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            margin-top: 15px;
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
                                <h2>Mon Panier</h2>
                                <p><a href="?controller=Store&action=index">Store</a> / Panier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cart-section">
            <div class="container">
                <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
                    <div class="alert alert-success text-center mb-5">
                        <i class="fas fa-check-circle fa-3x mb-3"></i><br>
                        <h4>Commande validée avec succès !</h4>
                        <p>Merci pour votre achat.</p>
                        <a href="?controller=Store&action=index" class="btn_1 mt-3">Retourner au store</a>
                    </div>
                <?php elseif (empty($items)): ?>
                    <div class="text-center">
                        <i class="fas fa-shopping-cart fa-4x mb-4" style="color: var(--muted);"></i>
                        <h3 class="text-white">Votre panier est vide</h3>
                        <p class="text-muted mb-4">Découvrez nos jeux et commencez votre collection</p>
                        <a href="?controller=Store&action=index" class="btn_1">Parcourir le Store</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <form action="?controller=Store&action=updateCart" method="post" id="cartForm">
                                <div class="table-responsive cart-table">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Prix</th>
                                                <th>Quantité</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo BASE_URL . $item['image']; ?>" alt=""
                                                                class="cart-item-img">
                                                            <div>
                                                                <h6 class="text-white mb-1">
                                                                    <?= htmlspecialchars($item['nom']) ?>
                                                                </h6>
                                                                <small class="text-muted">Réf: #<?= $item['id'] ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= number_format($item['prix'], 2) ?> DT</td>
                                                    <td>
                                                        <input type="number" name="quantities[<?= $item['id'] ?>]"
                                                            value="<?= $item['qty'] ?>" min="1" max="<?= $item['stock'] ?>"
                                                            class="qty-input"
                                                            onchange="document.getElementById('cartForm').submit()">
                                                    </td>
                                                    <td style="color: var(--accent); font-weight: bold;">
                                                        <?= number_format($item['line_total'], 2) ?> DT
                                                    </td>
                                                    <td>
                                                        <a href="?controller=Store&action=removeFromCart&id=<?= $item['id'] ?>"
                                                            class="btn-remove"><i class="fas fa-times"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <a href="?controller=Store&action=clearCart" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Vider le panier ?')">Vider le panier</a>
                                    <button type="submit" class="btn btn-outline-light btn-sm">Mettre à jour</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-4">
                            <div class="cart-summary">
                                <h4 class="text-white mb-4">Récapitulatif</h4>
                                <div class="summary-row">
                                    <span>Sous-total</span>
                                    <span><?= number_format($total, 2) ?> DT</span>
                                </div>
                                <div class="summary-row">
                                    <span>Livraison</span>
                                    <span>Gratuite</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total</span>
                                    <span style="color: var(--accent);"><?= number_format($total, 2) ?> DT</span>
                                </div>

                                <?php if (isset($_GET['error'])): ?>
                                    <div class="alert alert-danger mt-3"><?= htmlspecialchars(urldecode($_GET['error'])) ?>
                                    </div>
                                <?php endif; ?>

                                <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">

                                <form action="?controller=Store&action=checkout" method="post">
                                    <h5 class="text-white mb-3">Informations de livraison</h5>
                                    <div class="form-group mb-3">
                                        <input type="text" name="name" class="form-control" placeholder="Nom complet"
                                            required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="email" name="email" class="form-control" placeholder="Email" required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="tel" name="phone" class="form-control" placeholder="Téléphone" required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="address" class="form-control" placeholder="Adresse"
                                            required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="city" class="form-control" placeholder="Ville" required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-4">
                                        <select name="shipping" class="form-control" required
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                            <option value="" disabled selected>Mode de livraison</option>
                                            <option value="standard" style="color:black">Standard (3-5 jours)</option>
                                            <option value="express" style="color:black">Express (24h)</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn_1 w-100">Commander</button>
                                </form>
                            </div>
                        </div>
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