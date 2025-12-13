<?php
// $items contains cart items
// $total contains total price
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mon Panier - Engage Store</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        /* Cart Specific Styles */
        .cart-item-card {
            background: var(--panel);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }
        .cart-item-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 74, 87, 0.3);
        }
        .cart-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
        }
        .cart-details {
            flex: 1;
        }
        .cart-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .cart-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .cart-quantity-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 5px 10px;
            width: 80px;
            text-align: center;
        }
        .cart-summary-card {
            background: var(--panel);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 100px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--muted);
        }
        .summary-total {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 20px;
            font-size: 1.5rem;
            color: #fff;
            font-weight: 700;
        }
        .btn-remove {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-remove:hover {
            background: #dc3545;
            color: #fff;
        }
        
        /* Checkout Modal */
        .overlay-checkout {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        .checkout-modal {
            background: var(--panel);
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 74, 87, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            max-height: 90vh;
            overflow-y: auto;
        }
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }
        .checkout-header span {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        .checkout-input, .checkout-select {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 5px;
        }
        .checkout-label {
            color: var(--muted);
            margin-bottom: 8px;
            display: block;
        }
        .checkout-error {
            color: #dc3545;
            font-size: 0.85rem;
            min-height: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cart-item-card {
                flex-direction: column;
                text-align: center;
            }
            .cart-image {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="body_bg">
        <?php 
        // Set header context variables
        $headerShowUserMenu = isset($_SESSION['user_id']);
        $sessionUserName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Utilisateur';
        $sessionUserType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';
        
        // Include the common header
        include __DIR__ . '/../header_common.php'; 
        ?>

        <!-- Breadcrumb/Hero -->
        <section class="breadcrumb breadcrumb_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb_iner text-center">
                            <div class="breadcrumb_iner_item">
                                <h2>Votre Panier</h2>
                                <p>Finalisez votre commande et profitez de vos jeux</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cart Section -->
        <section class="section_padding">
            <div class="container">
                <?php if (empty($items)): ?>
                        <div class="no-games text-center">
                            <i class="fas fa-shopping-cart" style="font-size: 80px; color: #6c757d; margin-bottom: 20px;"></i>
                            <h3 class="no-games-title">Votre panier est vide</h3>
                            <p class="no-games-text mb-4">Découvrez nos jeux exclusifs et remplissez votre panier !</p>
                            <a href="?controller=Store&action=index" class="btn_1">Retour au Store</a>
                        </div>
                <?php else: ?>
                        <form method="post" action="?controller=Store&action=updateCart">
                            <div class="row">
                                <!-- Cart Items List -->
                                <div class="col-lg-8">
                                    <?php foreach ($items as $it): ?>
                                            <div class="cart-item-card">
                                                <?php if ($it['image']): ?>
                                                        <img class="cart-image" src="<?php echo BASE_URL . htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['nom']); ?>">
                                                <?php else: ?>
                                                        <img class="cart-image" src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png" alt="Default">
                                                <?php endif; ?>
                                        
                                                <div class="cart-details">
                                                    <h5 class="cart-title"><?php echo htmlspecialchars($it['nom']); ?></h5>
                                                    <div class="text-muted mb-2">Stock: <?php echo (int) $it['stock']; ?></div>
                                                    <div class="cart-price"><?php echo number_format($it['line_total'], 2); ?> DT</div>
                                                </div>

                                                <div class="d-flex flex-column align-items-end gap-2">
                                                    <input type="number" 
                                                           name="quantities[<?php echo (int) $it['id']; ?>]" 
                                                           value="<?php echo (int) $it['qty']; ?>" 
                                                           min="0" 
                                                           max="<?php echo (int) $it['stock']; ?>" 
                                                           class="cart-quantity-input">
                                                    <a href="?controller=Store&action=removeFromCart&id=<?php echo (int) $it['id']; ?>" 
                                                       class="btn-remove js-remove-line">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="?controller=Store&action=index" class="btn btn-outline-light">
                                            <i class="fas fa-arrow-left"></i> Continuer vos achats
                                        </a>
                                        <div>
                                            <a href="?controller=Store&action=clearCart" class="btn btn-outline-danger mr-2">Vider</a>
                                            <button type="submit" class="btn btn-outline-primary">Mettre à jour</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Panel -->
                                <div class="col-lg-4">
                                    <div class="cart-summary-card">
                                        <h4 class="text-white mb-4">Résumé de la commande</h4>
                                        <div class="summary-row">
                                            <span>Sous-total</span>
                                            <span class="text-white"><?php echo number_format($total, 2); ?> DT</span>
                                        </div>
                                        <div class="summary-row">
                                            <span>Livraison</span>
                                            <span class="text-success">Gratuite</span>
                                        </div>
                                        <div class="summary-row summary-total">
                                            <span>Total</span>
                                            <span style="color: var(--accent);"><?php echo number_format($total, 2); ?> DT</span>
                                        </div>
                                        <button type="button" class="btn_1 w-100 mt-4" id="btnCheckout">
                                            Passer la commande
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                <?php endif; ?>
            </div>
        </section>

        <!-- Order History Section -->
        <?php if (isset($orders) && !empty($orders)): ?>
            <section class="section_padding" style="padding-top: 0;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="section_tittle text-center">
                                <h2>Historique de vos commandes</h2>
                                <p>Retrouvez ici le détail de vos achats passés</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover" style="background: transparent;">
                                    <thead>
                                        <tr>
                                            <th>N° Commande</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Livraison</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo (int) $order['id']; ?></td>
                                                    <td><?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?></td>
                                                    <td style="color: var(--primary); font-weight: bold;"><?php echo number_format((float) $order['total'], 2); ?> DT</td>
                                                    <td>
                                                        <?php if ($order['shipping'] === 'express'): ?>
                                                                <span class="badge badge-warning"><i class="fas fa-bolt"></i> Express</span>
                                                        <?php else: ?>
                                                                <span class="badge badge-secondary"><i class="fas fa-truck"></i> Standard</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-spinner fa-spin"></i> En cours de traitement
                                                        </span>
                                                    </td>
                                                </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Footer -->
        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="<?php echo BASE_URL; ?>" class="footer_logo_iner">
                                    <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/logo.png" alt="logo">
                                </a>
                                <p>Engage - La plateforme de matchmaking pour le volontariat par le jeu vidéo</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Contact Info</h4>
                                <p>Adresse : Tunis, Tunisie</p>
                                <p>Email : contact@engage.tn</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Liens Importants</h4>
                                <ul class="list-unstyled">
                                    <li><a href="?controller=Store&action=index">Store</a></li>
                                    <li><a href="?controller=Partenaire&action=index">Partenaires</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <div id="mc_embed_signup">
                                    <form action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Email Address" class="placeholder hide-on-focus">
                                        <button type="submit" class="email_icon newsletter-submit">
                                            <i class="far fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copygight_text">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="copyright_text">
                                <p>© <script>document.write(new Date().getFullYear());</script> Engage. Tous droits réservés</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-instagram"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Checkout Modal -->
    <div id="overlayCheckout" class="overlay-checkout">
        <div class="checkout-modal">
            <div class="checkout-header">
                <span>Finaliser la commande</span>
                <button type="button" id="closeCheckout" class="btn btn-sm btn-outline-light"><i class="fas fa-times"></i></button>
            </div>
            <form id="checkoutForm" method="post" action="?controller=Store&action=checkout">
                <div class="checkout-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="checkout-label">Nom complet</label>
                                <input class="checkout-input" name="name" placeholder="Votre nom">
                                <div class="checkout-error" data-for="name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="checkout-label">Email</label>
                                <input class="checkout-input" name="email" placeholder="exemple@email.com">
                                <div class="checkout-error" data-for="email"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="checkout-label">Téléphone</label>
                        <input class="checkout-input" name="phone" placeholder="+216 00 000 000">
                        <div class="checkout-error" data-for="phone"></div>
                    </div>

                    <div class="mb-3">
                        <label class="checkout-label">Adresse de livraison</label>
                        <input class="checkout-input" name="address" placeholder="Rue, Appartement...">
                        <div class="checkout-error" data-for="address"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="checkout-label">Ville</label>
                                <input class="checkout-input" name="city" placeholder="Votre ville">
                                <div class="checkout-error" data-for="city"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="checkout-label">Mode de livraison</label>
                                <select class="checkout-select" name="shipping">
                                    <option value="">Choisir...</option>
                                    <option value="standard">Standard (Gratuit)</option>
                                    <option value="express">Express (+10 DT)</option>
                                </select>
                                <div class="checkout-error" data-for="shipping"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="checkout-label">Mode de paiement</label>
                        <div class="d-flex gap-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="paymentOnsite" name="payment_method" value="onsite" class="custom-control-input" checked>
                                <label class="custom-control-label text-white" for="paymentOnsite">Sur place (Espèces)</label>
                            </div>
                            <div class="custom-control custom-radio ml-3">
                                <input type="radio" id="paymentOnline" name="payment_method" value="online" class="custom-control-input">
                                <label class="custom-control-label text-white" for="paymentOnline">En ligne (Carte Bancaire)</label>
                            </div>
                        </div>
                        <div class="checkout-error" data-for="payment_method"></div>
                    </div>

                    <!-- Card Details (Hidden by default) -->
                    <div id="cardDetails" style="display: none; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; margin-top: 15px;">
                        <h6 class="text-white mb-3"><i class="fas fa-credit-card mr-2"></i>Informations de paiement</h6>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="checkout-label">Numéro de carte</label>
                                <input type="text" class="checkout-input" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19">
                                <div class="checkout-error" data-for="card_number"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="checkout-label">Expiration</label>
                                <input type="text" class="checkout-input" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                <div class="checkout-error" data-for="card_expiry"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="checkout-label">CVC</label>
                                <input type="text" class="checkout-input" name="card_cvc" placeholder="123" maxlength="3">
                                <div class="checkout-error" data-for="card_cvc"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkout-actions d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-light mr-2" id="cancelCheckout">Annuler</button>
                    <button type="submit" class="btn_1" id="submitCheckout">Confirmer la commande</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery.magnific-popup.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/owl.carousel.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/custom.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/cart.js"></script>
</body>
</html>