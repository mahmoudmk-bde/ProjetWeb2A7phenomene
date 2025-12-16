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
            padding: 5px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-remove:hover {
            background: var(--danger);
            color: white;
            text-decoration: none;
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
                                                            class="btn-remove"> Retirer</a>
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

                                <form action="?controller=Store&action=checkout" method="post"
                                    onsubmit="return validateForm()">
                                    <h5 class="text-white mb-3">Informations de livraison</h5>
                                    <div id="js-errors" class="alert alert-danger" style="display:none;"></div>

                                    <div class="form-group mb-3">
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Nom complet"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="email" id="email" class="form-control" placeholder="Email"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="phone" id="phone" class="form-control"
                                            placeholder="Téléphone"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="address" id="address" class="form-control"
                                            placeholder="Adresse"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="city" id="city" class="form-control" placeholder="Ville"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                    </div>
                                    <div class="form-group mb-4">
                                        <select name="shipping" id="shipping" class="form-control"
                                            style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                            <option value="" disabled selected>Mode de livraison</option>
                                            <option value="standard" style="color:black">Standard (3-5 jours)</option>
                                            <option value="express" style="color:black">Express (24h)</option>
                                        </select>
                                    </div>

                                    <!-- Payment Method Selection -->
                                    <h5 class="text-white mb-3 mt-4">Paiement</h5>
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="pay_onsite" name="payment_method" value="onsite"
                                                class="custom-control-input" checked onchange="togglePaymentFields()">
                                            <label class="custom-control-label text-white" for="pay_onsite">Payer sur place
                                                (Cash)</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="pay_online" name="payment_method" value="online"
                                                class="custom-control-input" onchange="togglePaymentFields()">
                                            <label class="custom-control-label text-white" for="pay_online">Payer en ligne
                                                (Carte Bancaire)</label>
                                        </div>
                                    </div>

                                    <!-- Credit Card Fields (Hidden by default) -->
                                    <div id="card-element-container"
                                        style="display: none; background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                                        <div class="form-group mb-3">
                                            <label class="text-white small">Numéro de carte</label>
                                            <input type="text" name="card_number" id="card_number" class="form-control"
                                                placeholder="0000 0000 0000 0000" maxlength="19"
                                                style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-0">
                                                    <label class="text-white small">Expiration (MM/YY)</label>
                                                    <input type="text" name="card_expiry" id="card_expiry"
                                                        class="form-control" placeholder="MM/YY" maxlength="5"
                                                        style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-0">
                                                    <label class="text-white small">CVC</label>
                                                    <input type="text" name="card_cvc" id="card_cvc" class="form-control"
                                                        placeholder="123" maxlength="4"
                                                        style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.1); color: white;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function togglePaymentFields() {
                                            var online = document.getElementById('pay_online').checked;
                                            var container = document.getElementById('card-element-container');

                                            if (online) {
                                                container.style.display = 'block';
                                            } else {
                                                container.style.display = 'none';
                                            }
                                        }

                                        function validateForm() {
                                            var errors = [];
                                            var name = document.getElementById('name').value;
                                            var email = document.getElementById('email').value;
                                            var phone = document.getElementById('phone').value;
                                            var address = document.getElementById('address').value;
                                            var city = document.getElementById('city').value;
                                            var shipping = document.getElementById('shipping').value;

                                            if (name.length < 3) errors.push("Le nom doit contenir au moins 3 caractères.");

                                            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                            if (!emailRegex.test(email)) errors.push("L'email n'est pas valide.");

                                            var phoneRegex = /^[0-9]{8,}$/;
                                            if (!phoneRegex.test(phone.replace(/\s/g, ''))) errors.push("Le numéro de téléphone doit contenir au moins 8 chiffres.");

                                            if (address.length < 5) errors.push("L'adresse est trop courte.");
                                            if (city.length < 2) errors.push("La ville est invalide.");
                                            if (shipping === "") errors.push("Veuillez choisir un mode de livraison.");

                                            if (document.getElementById('pay_online').checked) {
                                                var cn = document.getElementById('card_number').value;
                                                var ce = document.getElementById('card_expiry').value;
                                                var cc = document.getElementById('card_cvc').value;

                                                if (cn.length < 13) errors.push("Numéro de carte invalide.");
                                                if (ce.length < 5) errors.push("Date d'expiration invalide (MM/YY).");
                                                if (cc.length < 3) errors.push("CVC invalide.");
                                            }

                                            if (errors.length > 0) {
                                                var errorDiv = document.getElementById('js-errors');
                                                errorDiv.innerHTML = errors.join("<br>");
                                                errorDiv.style.display = 'block';
                                                return false;
                                            }
                                            return true;
                                        }
                                    </script>
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