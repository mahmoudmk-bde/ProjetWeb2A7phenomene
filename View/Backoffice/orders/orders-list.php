<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT'])
        ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']))
        : '';

    $basePath = '';
    if ($documentRoot && strpos($projectRoot, $documentRoot) === 0) {
        $basePath = substr($projectRoot, strlen($documentRoot));
    }
    $basePath = '/' . trim($basePath, '/');
    if ($basePath !== '/') {
        $basePath .= '/';
    }
    define('BASE_URL', $basePath === '//' ? '/' : $basePath);
}

require_once __DIR__ . '/../../../controller/AdminOrderController.php';
$orderC = new AdminOrderController();
$orders = $orderC->index();

$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Commandes - Engage Backoffice</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/all.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/custom-backoffice.css" />
    <style>
        /* Make content fill iframe completely */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            overflow-x: hidden;
        }
        #content {
            margin-left: 0 !important;
            padding: 20px !important;
            width: 100% !important;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Page Content (no sidebar for iframe) -->
    <div id="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestion des Commandes</h2>
                    <p style="color: var(--text-muted); margin: 0;">Suivez les commandes du store</p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des Commandes (<?php echo count($orders); ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Ville</th>
                                    <th>Livraison</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">Aucune commande</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $i => $o): ?>
                                        <tr>
                                            <td><?php echo (int)$o['id']; ?></td>
                                            <td><?php echo htmlspecialchars($o['name']); ?></td>
                                            <td>
                                                <div><?php echo htmlspecialchars($o['email']); ?></div>
                                                <small><?php echo htmlspecialchars($o['phone']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($o['city']); ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($o['shipping']); ?></span></td>
                                            <td><strong><?php echo number_format((float)$o['total'], 2); ?> DT</strong></td>
                                            <td><small><?php echo htmlspecialchars($o['created_at']); ?></small></td>
                                            <td>
                                                <a href="../router.php?controller=AdminOrder&action=show&id=<?php echo (int)$o['id']; ?>" class="btn btn-sm btn-info">Détail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
</body>
</html>

