<?php
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Panier - Engage Store</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
</head>
<body class="page-cart">
    <div class="body_bg">
        <section class="section_padding cart-section">
            <div class="container">
                <div class="cart-header">
                    <h2>Votre panier</h2>
                </div>
                <?php if (empty($items)): ?>
                    <div class="no-games">
                        <h3 class="no-games-title">Panier vide</h3>
                        <p class="no-games-text">Ajoutez des jeux depuis le store.</p>
                        <!-- BOUTON RETOUR AU STORE QUAND PANIER VIDE -->
                        <div class="cart-actions" style="margin-top: 30px; justify-content: center;">
                            <a href="?controller=Store&action=index" class="btn-cart-gray" id="btnBackStoreEmpty">
                                ← Retour au Store
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="post" action="?controller=Store&action=updateCart">
                        <div class="row cart-page">
                            <div class="col-lg-8 cart-items">
                                <?php foreach ($items as $it): ?>
                                    <div class="game-card cart-item-card">
                                        <div class="row cart-line">
                                            <div class="col-3">
                                                <?php if ($it['image']): ?>
                                                    <img class="cart-image" src="<?php echo BASE_URL . htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['nom']); ?>">
                                                <?php else: ?>
                                                    <img class="cart-image" src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png" alt="<?php echo htmlspecialchars($it['nom']); ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-5">
                                                <h5 class="game-title"><?php echo htmlspecialchars($it['nom']); ?></h5>
                                                <div class="cart-stock">Stock: <?php echo (int)$it['stock']; ?></div>
                                            </div>
                                            <div class="col-2">
                                                <input type="number" name="quantities[<?php echo (int)$it['id']; ?>]" value="<?php echo (int)$it['qty']; ?>" min="0" max="<?php echo (int)$it['stock']; ?>" class="form-control cart-quantity">
                                            </div>
                                            <div class="col-2 cart-line-total">
                                                <div class="cart-price">
                                                    <?php echo number_format($it['line_total'], 2); ?> DT
                                                </div>
                                                <a href="?controller=Store&action=removeFromCart&id=<?php echo (int)$it['id']; ?>" class="btn-view-game js-remove-line cart-remove-btn">
                                                    Retirer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="cart-actions">
                                    <button type="submit" class="btn-cart-gray" id="btnUpdateCart">Mettre à jour</button>
                                    <a href="?controller=Store&action=clearCart" class="btn-cart-gray" id="btnClearCart">Vider le panier</a>
                                    <a href="?controller=Store&action=index" class="btn-cart-gray" id="btnBackStore">Retour au Store</a>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="game-detail-card cart-summary">
                                    <h4 class="cart-summary-title">Résumé</h4>
                                    <div class="cart-total-row">
                                        <span class="cart-total-label">Total</span>
                                        <span class="cart-total-amount"><?php echo number_format($total, 2); ?> DT</span>
                                    </div>
                                    <a href="#" class="btn-cart-action" id="btnCheckout">Commander</a>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/bootstrap.min.js"></script>
    <div id="cartToast" class="toast-notice"></div>
    <div id="overlayCheckout" class="overlay-checkout">
        <div class="checkout-modal">
            <div class="checkout-header">
                <span>Commander</span>
                <button type="button" id="closeCheckout" class="btn-cart-gray">Fermer</button>
            </div>
            <form id="checkoutForm" method="post" action="?controller=Store&action=checkout">
                <div class="checkout-body">
                    <div class="checkout-row">
                        <label class="checkout-label">Nom et prénom</label>
                        <input class="checkout-input" name="name">
                        <div class="checkout-error" data-for="name"></div>
                    </div>
                    <div class="checkout-row">
                        <label class="checkout-label">Email</label>
                        <input class="checkout-input" name="email">
                        <div class="checkout-error" data-for="email"></div>
                    </div>
                    <div class="checkout-row">
                        <label class="checkout-label">Téléphone</label>
                        <input class="checkout-input" name="phone">
                        <div class="checkout-error" data-for="phone"></div>
                    </div>
                    <div class="checkout-row">
                        <label class="checkout-label">Adresse</label>
                        <input class="checkout-input" name="address">
                        <div class="checkout-error" data-for="address"></div>
                    </div>
                    <div class="checkout-row">
                        <label class="checkout-label">Ville</label>
                        <input class="checkout-input" name="city">
                        <div class="checkout-error" data-for="city"></div>
                    </div>
                    <div class="checkout-row">
                        <label class="checkout-label">Livraison</label>
                        <select class="checkout-select" name="shipping">
                            <option value="">Choisir</option>
                            <option value="standard">Standard</option>
                            <option value="express">Express</option>
                        </select>
                        <div class="checkout-error" data-for="shipping"></div>
                    </div>
                </div>
                <div class="checkout-actions">
                    <button type="button" class="btn-cart-gray" id="cancelCheckout">Annuler</button>
                    <button type="submit" class="btn-cart-action" id="submitCheckout">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/cart.js"></script>
</body>
</html>