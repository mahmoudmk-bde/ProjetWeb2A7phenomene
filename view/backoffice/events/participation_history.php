<?php
session_start();
// Load project DB config (fallback if not already loaded)
if (!class_exists('config')) {
    $dbCfg = __DIR__ . '/../../../db_config.php';
    if (file_exists($dbCfg)) {
        require_once $dbCfg;
    }
}
require_once __DIR__ . '/../../../model/evenementModel.php';
require_once __DIR__ . '/../../../model/participationModel.php';

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

$participationModel = new ParticipationModel();
$allHistory = $participationModel->getAllParticipationsWithUsers();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6; // afficher 6 participations par page
$totalHistory = count($allHistory);
$totalPages = (int) ceil($totalHistory / $perPage);
$offset = ($page - 1) * $perPage;
$history = array_slice($allHistory, $offset, $perPage);

$pageTitle = "Historique Global des Participations";

// Vérifier si l'export CSV est demandé
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=historique_participations_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // En-têtes du CSV
    fputcsv($output, ['ID', 'Participant', 'Email', 'Événement', 'Date Événement', 'Date Participation', 'Quantité', 'Montant', 'Mode Paiement', 'Référence', 'Statut']);
    
    foreach ($allHistory as $row) {
        $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
        $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
            ? $row['montant_total']
            : ($row['type_evenement'] === 'payant'
                ? $qty * (float)($row['prix'] ?? 0)
                : 0);
        $modeDisplay = $row['mode_paiement'] ?? ($row['type_evenement'] === 'payant' ? 'Carte' : 'Gratuit');
        
        fputcsv($output, [
            $row['id_participation'],
            html_entity_decode($row['prenom'] . ' ' . $row['nom']),
            $row['email'],
            html_entity_decode($row['titre']),
            $row['date_evenement'] ?? '',
            $row['date_participation'],
            $qty,
            $amountDisplay,
            html_entity_decode($modeDisplay),
            html_entity_decode($row['reference_paiement'] ?? ''),
            html_entity_decode($row['statut'])
        ]);
    }
    
    fclose($output);
    exit();
}

$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= $pageTitle ?> - Engage Backoffice</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/all.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/custom-backoffice.css" />
    <style>
        /* Maintain consistent iframe styling */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            overflow-x: hidden;
            background-color: #1a1a2e;
        }
        #content {
            margin-left: 0 !important;
            padding: 20px !important;
            width: 100% !important;
            min-height: 100vh;
        }
        
        /* Pagination custom styles */
        .pagination { display: flex; gap: 6px; justify-content: center; padding-left: 0; list-style: none; }
        .pagination .page-item .page-link {
            color: #212529;
            background: #fff;
            border: 1px solid #e6e6e6;
            padding: 8px 12px;
            border-radius: 6px;
            min-width: 40px;
            text-align: center;
            transition: all .12s ease;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(45deg, #ff4a57, #ff6b6b);
            color: #fff;
            border-color: rgba(0,0,0,0.06);
            box-shadow: 0 6px 14px rgba(255,74,87,0.16);
        }
        .pagination .page-item .page-link:hover { transform: translateY(-3px); }
        .pagination .page-item.disabled .page-link { opacity: .5; pointer-events: none; }
        @media (max-width: 768px) { .pagination .page-item .page-link { padding: 6px 8px; min-width: 34px; } }
    </style>
</head>

<body>
    <div id="content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-history text-primary"></i> <?= $pageTitle ?></h2>
                    <p style="color: var(--text-muted); margin: 0;">Consultez l'historique de toutes les participations aux événements</p>
                </div>
                <div class="btn-group">
                    <a href="evenement.php" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <?php if (!empty($allHistory)): ?>
                        <a href="?export=csv" class="btn btn-success">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Statistiques rapides -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-list-alt"></i>
                        <h3><?= count($allHistory) ?></h3>
                        <p>Total Participations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <h3>
                            <?= count(array_filter($allHistory, fn($p) => $p['statut'] === 'acceptée' || $p['statut'] === 'acceptee')) ?>
                        </h3>
                        <p>Acceptées</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-money-bill-wave" style="color: var(--warning);"></i>
                        <h3>
                            <?php 
                                $totalRevenu = 0;
                                foreach ($allHistory as $p) {
                                    if ($p['statut'] === 'acceptée' || $p['statut'] === 'acceptee') {
                                        if (isset($p['montant_total']) && $p['montant_total'] !== null) {
                                            $totalRevenu += (float)$p['montant_total'];
                                        } elseif ($p['type_evenement'] === 'payant') {
                                            $qty = isset($p['quantite']) ? max(1, (int)$p['quantite']) : 1;
                                            $totalRevenu += $qty * (float)($p['prix'] ?? 0);
                                        }
                                    }
                                }
                                echo number_format($totalRevenu, 2);
                            ?> <small style="font-size:0.5em;">TND</small>
                        </h3>
                        <p>Revenu Total</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-users" style="color: var(--info);"></i>
                        <h3>
                            <?php
                                $uniqueUsers = [];
                                foreach ($allHistory as $p) {
                                    $key = strtolower(trim($p['email']));
                                    $uniqueUsers[$key] = true;
                                }
                                echo count($uniqueUsers);
                            ?>
                        </h3>
                        <p>Participants Uniques</p>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Liste des Participations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Participant</th>
                                    <th>Événement</th>
                                    <th>Date</th>
                                    <th>Qté</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($history)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-history"></i>
                                                <p>Aucune participation trouvée</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($history as $row): 
                                        $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
                                        $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
                                            ? number_format((float)$row['montant_total'], 2) . ' TND'
                                            : ($row['type_evenement'] === 'payant'
                                                ? number_format($qty * (float)($row['prix'] ?? 0), 2) . ' TND'
                                                : 'Gratuit');
                                        
                                        $statusClass = 'badge-secondary';
                                        $statusIcon = '';
                                        switch(strtolower($row['statut'])) {
                                            case 'en attente':
                                                $statusClass = 'badge-warning';
                                                $statusIcon = '⏳';
                                                break;
                                            case 'acceptée':
                                            case 'acceptee':
                                                $statusClass = 'badge-success';
                                                $statusIcon = '✅';
                                                break;
                                            case 'refusée':
                                            case 'refusee':
                                                $statusClass = 'badge-danger';
                                                $statusIcon = '❌';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td><?= $row['id_participation'] ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold"><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></span>
                                                    <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['titre']) ?>
                                            </td>
                                            <td>
                                                <small><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($row['date_participation'])) ?></small>
                                                <br>
                                                <small class="text-muted"><?= date('H:i', strtotime($row['date_participation'])) ?></small>
                                            </td>
                                            <td><?= $qty ?></td>
                                            <td>
                                                <span class="font-weight-bold text-primary"><?= $amountDisplay ?></span>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($row['mode_paiement'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= $statusIcon ?> <?= ucfirst($row['statut']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="text-center mt-4 mb-4">
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= max(1, $page-1) ?>" aria-label="Précédent">&laquo;</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= min($totalPages, $page+1) ?>" aria-label="Suivant">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
</body>
</html>
