<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Détail Commande - Engage Backoffice</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/all.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/custom-backoffice.css" />
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="<?php echo $ordersAssetsBase; ?>img/logo.png" alt="logo" style="height: 40px;" /> ENGAGE</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
            <li class="active">
                <a href="#gamificationSubmenu" data-toggle="collapse" aria-expanded="true">Gamification</a>
                <ul class="collapse show list-unstyled" id="gamificationSubmenu">
                    <li><a href="?controller=AdminPartenaire&action=index">Partenaires</a></li>
                    <li><a href="?controller=AdminStore&action=index">Store Items</a></li>
                    <li class="active"><a href="?controller=AdminOrder&action=index">Commandes</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Commande #<?php echo isset($order['id']) ? (int)$order['id'] : 0; ?></h2>
                    <p style="color: var(--text-muted); margin: 0;">Détails de la commande et des articles</p>
                </div>
                <a href="?controller=AdminOrder&action=index" class="btn btn-secondary">Retour</a>
            </div>

            <?php if (!$order): ?>
                <div class="alert alert-danger">Commande introuvable</div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-header">Informations Client</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nom:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Adresse:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                                <p><strong>Ville:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                                <p><strong>Livraison:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($order['shipping']); ?></span></p>
                            </div>
                        </div>
                        <p class="mt-2"><strong>Total:</strong> <?php echo number_format((float)$order['total'], 2); ?> DT</p>
                        <p><small><strong>Créée le:</strong> <?php echo htmlspecialchars($order['created_at']); ?></small></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Articles</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th># Item</th>
                                        <th>Nom</th>
                                        <th>Prix</th>
                                        <th>Qté</th>
                                        <th>Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($items)): ?>
                                        <tr><td colspan="5" class="text-center">Aucun article</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($items as $it): ?>
                                            <tr>
                                                <td><?php echo (int)$it['item_id']; ?></td>
                                                <td><?php echo htmlspecialchars($it['name']); ?></td>
                                                <td><?php echo number_format((float)$it['price'], 2); ?> DT</td>
                                                <td><?php echo (int)$it['qty']; ?></td>
                                                <td><?php echo number_format(((float)$it['price'] * (int)$it['qty']), 2); ?> DT</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
</body>
</html>

