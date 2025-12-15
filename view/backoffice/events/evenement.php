<?php
session_start();
// Use project DB config; fallback path relative to this file
if (!class_exists('config')) {
    $dbCfg = __DIR__ . '/../../../db_config.php';
    if (file_exists($dbCfg)) {
        require_once $dbCfg;
    }
}
require_once __DIR__ . '/../../../model/evenementModel.php';

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
    header('Location: evenement.php');
    exit;
}

// Get all events
$allEvents = $eventModel->getAllEvents();
// Simple pagination to match missions list layout
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 2;
$totalEvents = is_array($allEvents) ? count($allEvents) : 0;
$totalPages = $totalEvents > 0 ? (int)ceil($totalEvents / $perPage) : 1;
$offset = ($page - 1) * $perPage;
$pageEvents = array_slice($allEvents, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Événements - ENGAGE Admin</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .missions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .missions-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .mission-stat-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .mission-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .mission-stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .mission-stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .add-mission-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .add-mission-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
        }
        
        .mission-card {
            background: var(--accent-color);
            padding: 15px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .mission-header {
            margin-bottom: 8px;
        }
        
        .mission-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 4px 0;
        }
        
        .mission-difficulty {
            background: var(--secondary-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .difficulty-facile { color: #28a745; border: 1px solid #28a745; }
        .difficulty-moyen { color: #ffc107; border: 1px solid #ffc107; }
        .difficulty-difficile { color: #dc3545; border: 1px solid #dc3545; }
        
        .mission-dates {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 8px;
            font-size: 0.75rem;
        }
        
        .date-info {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--text-muted);
        }
        
        .date-info i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }
        
        .mission-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
            flex: 1;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px;
            background: var(--secondary-color);
            border-radius: 6px;
            font-size: 0.75rem;
        }
        
        .detail-item i {
            color: var(--primary-color);
            width: 12px;
            text-align: center;
            flex-shrink: 0;
            font-size: 0.75rem;
        }
        
        .detail-label {
            color: var(--text-muted);
            font-size: 0.65rem;
            display: block;
        }
        
        .detail-value {
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .mission-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.95rem;
            cursor: pointer;
        }
        
        .btn-icon:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn-candidatures {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-candidatures:hover {
            background: linear-gradient(45deg, #0056b3, #004099);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        
        .btn-modifier {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            color: #212529;
        }
        
        .btn-modifier:hover {
            background: linear-gradient(45deg, #e0a800, #d39e00);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        
        .btn-supprimer {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-supprimer:hover {
            background: linear-gradient(45deg, #c82333, #bd2130);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--text-muted);
            opacity: 0.5;
        }
        /* Grille : afficher 2 cartes côte-à-côte sur desktop, 1 sur mobile */
        .mission-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .mission-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
    <style>
        /* Pagination styles for backoffice to match admin theme */
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

<div class="container mt-4">

    <!-- En-tête avec statistiques -->
    <div class="missions-header">
        <h2 class="text-white">🎯 Gestion des Événements</h2>
        <a href="createevent.php" class="add-mission-btn">
            <i class="fas fa-plus"></i> Nouvelle Événement
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Statistiques des événements -->
    <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= count($allEvents) ?></span>
            <span class="mission-stat-label">Total Événements</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?php 
                    $totalParticipants = 0;
                    foreach ($allEvents as $event) {
                        $totalParticipants += (int)$eventModel->countParticipants($event['id_evenement']);
                    }
                    echo $totalParticipants;
                ?>
            </span>
            <span class="mission-stat-label">Total Participants</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?php 
                    $totalVues = 0;
                    foreach ($allEvents as $event) {
                        $totalVues += (int)($event['vues'] ?? 0);
                    }
                    echo $totalVues;
                ?>
            </span>
            <span class="mission-stat-label">Total Vues</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($allEvents, function($e) { 
                    return strtotime($e['date_evenement']) >= strtotime('today'); 
                })) ?>
            </span>
            <span class="mission-stat-label">Événements à Venir</span>
        </div>
    </div>

    <!-- Grille des événements -->
    <div class="mission-grid">
        <?php if (empty($pageEvents)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <h3>Aucun événement trouvé</h3>
                <p>Commencez par créer votre premier événement</p>
                <a href="createevent.php" class="add-mission-btn" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Créer un événement
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($pageEvents as $event): ?>
                <div class="mission-card">
                    <!-- En-tête de l'événement -->
                    <div class="mission-header">
                        <div>
                            <h3 class="mission-title"><?= htmlspecialchars($event['titre']) ?></h3>
                            <span class="mission-difficulty difficulty-facile">
                                Événement
                            </span>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="mission-dates">
                        <div class="date-info">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></span>
                        </div>
                        <?php if (!empty($event['heure_evenement'])): ?>
                        <div class="date-info">
                            <i class="far fa-clock"></i>
                            <span><?= htmlspecialchars(substr($event['heure_evenement'],0,5)) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Détails de l'événement -->
                    <div class="mission-details">
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <div class="detail-label">Lieu</div>
                                <div class="detail-value"><?= htmlspecialchars($event['lieu']) ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($event['duree_minutes'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-hourglass-half"></i>
                            <div>
                                <div class="detail-label">Durée</div>
                                <div class="detail-value"><?= (int)$event['duree_minutes'] ?> min</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($event['description'])): ?>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <div class="detail-label">Description</div>
                                <div class="detail-value" style="font-weight: normal; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars(substr($event['description'], 0, 50)) ?>
                                    <?= strlen($event['description']) > 50 ? '...' : '' ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nombre de participants et vues (style Facebook) -->
                    <div class="mission-likes-info" style="margin: 12px 0; padding: 10px; background: rgba(255, 74, 87, 0.1); border-radius: 8px; border-left: 3px solid #ff4a57; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="color: #ff4a57; font-size: 1rem;"></i>
                        <span style="color: var(--text-color); font-weight: 600; font-size: 0.9rem;">
                            <?= (int)$eventModel->countParticipants($event['id_evenement']) ?> participants • <?= (int)($event['vues'] ?? 0) ?> vues
                        </span>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mission-actions">
                        <!-- Participants -->
                        <a href="participation.php?event_id=<?= $event['id_evenement'] ?>"
                           class="btn-icon btn-candidatures"
                           title="Voir les participants">
                            <i class="fas fa-users"></i>
                        </a>

                        <!-- Modifier -->
                        <a href="editevent.php?id=<?= $event['id_evenement'] ?>" 
                           class="btn-icon btn-modifier"
                           title="Modifier l'événement">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Supprimer -->
                        <a href="evenement.php?action=delete&id=<?= $event['id_evenement'] ?>" 
                           class="btn-icon btn-supprimer"
                           title="Supprimer l'événement"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination (même structure que frontoffice) -->
    <?php if ($totalPages > 1): ?>
    <div class="text-center mt-4">
        <nav aria-label="Pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= max(1, $page-1) ?>" aria-label="Précédent">&laquo;</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= min($totalPages, $page+1) ?>" aria-label="Suivant">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

</body>
</html>