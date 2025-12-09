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

require_once __DIR__ . '/../../../controller/AdminStoreController.php';
$storeC = new AdminStoreController();
$items = $storeC->index();

$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Store - Jeux vidéo - Engage Backoffice</title>
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
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-store text-primary"></i> Gestion du Store</h2>
                    <p style="color: var(--text-muted); margin: 0;">Gérez les jeux vidéo disponibles dans le store</p>
                </div>
                <a href="../router.php?controller=AdminStore&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un jeu
                </a>
            </div>

            <!-- Messages de feedback -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Statistiques rapides -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-gamepad"></i>
                        <h3><?= count($items) ?></h3>
                        <p>Jeux au total</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-box" style="color: var(--success);"></i>
                        <h3>
                            <?php 
                            $totalStock = array_sum(array_column($items, 'stock'));
                            echo number_format($totalStock);
                            ?>
                        </h3>
                        <p>Items en stock</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-dollar-sign" style="color: var(--warning);"></i>
                        <h3>
                            <?php 
                            if (count($items) > 0) {
                                $avgPrice = array_sum(array_column($items, 'prix')) / count($items);
                                echo number_format($avgPrice, 2) . ' DT';
                            } else {
                                echo '0 DT';
                            }
                            ?>
                        </h3>
                        <p>Prix moyen</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                        <h3>
                            <?php 
                            $lowStock = count(array_filter($items, function($item) {
                                return $item['stock'] < 5;
                            }));
                            echo $lowStock;
                            ?>
                        </h3>
                        <p>Stock faible</p>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Jeux (<?= count($items) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Nom du Jeu</th>
                                    <th>Partenaire</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Catégorie</th>
                                    <th>Plateforme</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-gamepad"></i>
                                                <p>Aucun jeu dans le store</p>
                                                <a href="../router.php?controller=AdminStore&action=create" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus"></i> Ajouter le premier jeu
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?php if ($item['image']): ?>
                                                    <img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" 
                                                         alt="<?= htmlspecialchars($item['nom']) ?>" 
                                                         class="game-image">
                                                <?php else: ?>
                                                    <div class="game-image d-flex align-items-center justify-content-center" 
                                                         style="background: var(--accent-color);">
                                                        <i class="fas fa-image" style="color: var(--text-muted);"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($item['nom']) ?></strong>
                                                <?php if ($item['age_minimum']): ?>
                                                    <br>
                                                    <small style="color: var(--text-muted);">
                                                        <i class="fas fa-child"></i> <?= $item['age_minimum'] ?>+ ans
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= htmlspecialchars($item['partenaire_nom'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong style="color: var(--primary-color);">
                                                    <?= number_format($item['prix'], 2) ?> DT
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                $stockClass = 'stock-good';
                                                if ($item['stock'] == 0) {
                                                    $stockClass = 'stock-low';
                                                } elseif ($item['stock'] < 5) {
                                                    $stockClass = 'stock-low';
                                                } elseif ($item['stock'] < 20) {
                                                    $stockClass = 'stock-medium';
                                                }
                                                ?>
                                                <span class="badge stock-badge <?= $stockClass ?>">
                                                    <?= $item['stock'] ?>
                                                </span>
                                                <?php if ($item['stock'] == 0): ?>
                                                    <br><small style="color: var(--danger);">Rupture</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $categoryBadges = [
                                                    'action' => 'badge-action',
                                                    'aventure' => 'badge-aventure',
                                                    'sport' => 'badge-sport',
                                                    'strategie' => 'badge-strategie',
                                                    'simulation' => 'badge-simulation',
                                                    'rpg' => 'badge-rpg'
                                                ];
                                                $badgeClass = $categoryBadges[$item['categorie']] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= ucfirst($item['categorie']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($item['plateforme']): ?>
                                                    <small style="color: var(--text-muted);">
                                                        <i class="fas fa-desktop"></i> 
                                                        <?= htmlspecialchars($item['plateforme']) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small style="color: var(--text-muted);">N/A</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="../router.php?controller=AdminStore&action=edit&id=<?= $item['id'] ?>" 
                                                       class="btn btn-sm btn-info" 
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../router.php?controller=AdminStore&action=delete&id=<?= $item['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce jeu ?\n\nCette action est irréversible !')"
                                                       title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
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

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo $ordersAssetsBase; ?>js/partenaire-form.js"></script>
    
    <script>
        // Toggle sidebar on mobile
        $(document).ready(function() {
            if ($(window).width() < 768) {
                $('#sidebar').hide();
            }
        });
    </script>
</body>
</html>
