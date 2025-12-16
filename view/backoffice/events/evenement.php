<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Use project DB config; fallback path relative to this file
if (!class_exists('config')) {
    $dbCfg = __DIR__ . '/../../../db_config.php';
    if (file_exists($dbCfg)) {
        require_once $dbCfg;
    }
}
require_once __DIR__ . '/../../../model/evenementModel.php';

// Define BASE_URL if not already defined (copied logic from reference)
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

// Simuler une session d'administration si nécessaire
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
}

$eventModel = new EvenementModel();

// Gérer l'action de suppression
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($eventModel->delete($_GET['id'])) {
        $_SESSION['success'] = "Événement supprimé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'événement";
    }
    // Redirect to remove query params
    echo "<script>window.location.href='evenement.php';</script>";
    exit;
}

// Get all events
$allEvents = $eventModel->getAllEvents();

// Pagination Logic
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$totalItems = count($allEvents);
$totalPages = ceil($totalItems / $limit);

if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
}

$events = array_slice($allEvents, ($page - 1) * $limit, $limit);

// Path to assets (adjusted for depth)
$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Gestion des Événements - Engage Backoffice</title>
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
            /* background-color handled by custom-backoffice.css */
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

        /* Prevent button jump in table */
        .table .btn:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body>
    <div id="content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-calendar-alt text-primary"></i> Gestion des Événements</h2>
                    <p style="color: var(--text-muted); margin: 0;">Gérez les événements et leurs détails</p>
                </div>
                <a href="createevent.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvel Événement
                </a>
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
                        <i class="fas fa-calendar-check"></i>
                        <h3><?= $totalItems ?></h3>
                        <p>Total Événements</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-users" style="color: var(--success);"></i>
                        <h3>
                            <?php 
                            $totalParticipants = 0;
                            foreach ($allEvents as $e) {
                                $totalParticipants += (int)$eventModel->countParticipants($e['id_evenement']);
                            }
                            echo number_format($totalParticipants);
                            ?>
                        </h3>
                        <p>Total Participants</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-eye" style="color: var(--warning);"></i>
                        <h3>
                            <?php 
                            $totalVues = 0;
                            foreach ($allEvents as $e) {
                                $totalVues += (int)($e['vues'] ?? 0);
                            }
                            echo number_format($totalVues);
                            ?>
                        </h3>
                        <p>Vues Totales</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-hourglass-half" style="color: var(--danger);"></i>
                        <h3>
                            <?= count(array_filter($allEvents, function($e) { 
                                return strtotime($e['date_evenement']) >= strtotime('today'); 
                            })) ?>
                        </h3>
                        <p>Événements à venir</p>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Événements (<?= $totalItems ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Titre</th>
                                    <th>Date & Lieu</th>
                                    <th>Type</th>
                                    <th>Prix</th>
                                    <th>Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($events)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-times"></i>
                                                <p>Aucun événement trouvé</p>
                                                <a href="createevent.php" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus"></i> Créer un événement
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($events as $index => $event): 
                                        $participants = (int)$eventModel->countParticipants($event['id_evenement']);
                                        $vues = (int)($event['vues'] ?? 0);
                                        $isPayant = ($event['type_evenement'] === 'payant' || $event['prix'] > 0);
                                    ?>
                                        <tr>
                                            <td><?= ($page - 1) * $limit + $index + 1 ?></td>
                                            <td>
                                                <?php if ($event['image']): ?>
                                                    <img src="<?php echo BASE_URL . 'view/frontoffice/assets/img/events/' . htmlspecialchars($event['image']); ?>"
                                                        alt="<?= htmlspecialchars($event['titre']) ?>" class="game-image"
                                                        onerror="this.src='<?php echo BASE_URL; ?>view/frontoffice/assets/img/events/event-default.jpg'">
                                                <?php else: ?>
                                                    <div class="game-image d-flex align-items-center justify-content-center"
                                                        style="background: var(--accent-color);">
                                                        <i class="fas fa-image" style="color: var(--text-muted);"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($event['titre']) ?></strong>
                                            </td>
                                            <td>
                                                <small style="display:block;"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($event['date_evenement'])) ?> à <?= substr($event['heure_evenement'], 0, 5) ?></small>
                                                <small style="color: var(--text-muted);"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lieu']) ?></small>
                                            </td>
                                            <td>
                                                <?php if($isPayant): ?>
                                                    <span class="badge badge-warning">Payant</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Gratuit</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($isPayant): ?>
                                                    <strong style="color: var(--primary-color);"><?= number_format($event['prix'], 2) ?> DT</strong>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><i class="fas fa-users"></i> <?= $participants ?></span>
                                                <span class="badge badge-secondary"><i class="fas fa-eye"></i> <?= $vues ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="participation.php?event_id=<?= $event['id_evenement'] ?>"
                                                        class="btn btn-sm btn-primary" title="Voir Participants">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="editevent.php?id=<?= $event['id_evenement'] ?>"
                                                        class="btn btn-sm btn-info" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="evenement.php?action=delete&id=<?= $event['id_evenement'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet événement ?')"
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