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

require_once __DIR__ . '/../../../controller/AdminPartenaireController.php';
$partenaireC = new AdminPartenaireController();
$partenaires = $partenaireC->index();

$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Partenaires - Engage Backoffice</title>
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
                    <h2><i class="fas fa-handshake text-primary"></i> Gestion des Partenaires</h2>
                    <p style="color: var(--text-muted); margin: 0;">Gérez les partenaires de la plateforme</p>
                </div>
                <a href="../router.php?controller=AdminPartenaire&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Partenaire
                </a>
            </div>

            <!-- Messages -->
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

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Partenaires (<?= count($partenaires) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Logo</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($partenaires)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <p>Aucun partenaire trouvé</p>
                                                <a href="../router.php?controller=AdminPartenaire&action=create" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Créer le premier partenaire
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($partenaires as $index => $partenaire): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?php if ($partenaire['logo']): ?>
                                                    <img src="<?php echo BASE_URL . htmlspecialchars($partenaire['logo']); ?>" 
                                                         alt="Logo" class="partner-logo">
                                                <?php else: ?>
                                                    <div class="partner-logo d-flex align-items-center justify-content-center" 
                                                         style="background: var(--accent-color);">
                                                        <i class="fas fa-image" style="color: var(--text-muted);"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= htmlspecialchars($partenaire['nom']) ?></strong></td>
                                            <td><?= htmlspecialchars($partenaire['email'] ?? '—') ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php
                                                    $types = [
                                                        'sponsor' => '<i class="fas fa-dollar-sign"></i> Sponsor',
                                                        'testeur' => '<i class="fas fa-flask"></i> Testeur',
                                                        'vendeur' => '<i class="fas fa-store"></i> Vendeur'
                                                    ];
                                                    echo $types[$partenaire['type']] ?? ucfirst($partenaire['type']);
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'actif' => 'badge-success',
                                                    'inactif' => 'badge-secondary',
                                                    'en_attente' => 'badge-warning'
                                                ];
                                                $badge = $badges[$partenaire['statut']] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badge ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $partenaire['statut'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($partenaire['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="../router.php?controller=AdminPartenaire&action=edit&id=<?= $partenaire['id'] ?>" 
                                                       class="btn btn-sm btn-info" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../router.php?controller=AdminPartenaire&action=delete&id=<?= $partenaire['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce partenaire ?\n\nCette action est irréversible !')"
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
