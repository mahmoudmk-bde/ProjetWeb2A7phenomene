<?php 
require_once __DIR__ . '/../../../controller/missioncontroller.php';
<<<<<<< HEAD
require_once __DIR__ . '/../../../controller/LikeController.php';

$missionC = new missioncontroller();
$likeController = new LikeController();

// Pagination for backoffice list
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 2; // afficher 2 missions par page
$pag = $missionC->getMissionsPaginated($page, $perPage);
$missions = $pag['data'];
$totalMissions = $pag['total'];
$totalPages = (int) ceil($totalMissions / $perPage);

// Keep all missions for stats (small dataset expected)
$allMissions = $missionC->missionliste();

// Récupérer les likes pour toutes les missions en une fois (optimisation)
$missionIds = array_column($missions, 'id');
$likesCount = $likeController->getLikesCountForMissions($missionIds);
=======
$missionC = new missioncontroller();
$missions = $missionC->missionliste();
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des missions - ENGAGE Admin</title>

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
<<<<<<< HEAD
            padding: 15px;
=======
            padding: 25px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
<<<<<<< HEAD
            display: flex;
            flex-direction: column;
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .mission-header {
<<<<<<< HEAD
            margin-bottom: 8px;
        }
        
        .mission-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 4px 0;
=======
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .mission-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            flex: 1;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .mission-difficulty {
            background: var(--secondary-color);
<<<<<<< HEAD
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
=======
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 15px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .difficulty-facile { color: #28a745; border: 1px solid #28a745; }
        .difficulty-moyen { color: #ffc107; border: 1px solid #ffc107; }
        .difficulty-difficile { color: #dc3545; border: 1px solid #dc3545; }
        
        .mission-dates {
            display: flex;
<<<<<<< HEAD
            flex-direction: column;
            gap: 4px;
            margin-bottom: 8px;
            font-size: 0.75rem;
=======
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .date-info {
            display: flex;
            align-items: center;
<<<<<<< HEAD
            gap: 4px;
            color: var(--text-muted);
=======
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .date-info i {
            color: var(--primary-color);
<<<<<<< HEAD
            font-size: 0.8rem;
        }
        
        .mission-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
            flex: 1;
=======
        }
        
        .mission-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .detail-item {
            display: flex;
            align-items: center;
<<<<<<< HEAD
            gap: 6px;
            padding: 6px;
            background: var(--secondary-color);
            border-radius: 6px;
            font-size: 0.75rem;
=======
            gap: 10px;
            padding: 10px;
            background: var(--secondary-color);
            border-radius: 8px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .detail-item i {
            color: var(--primary-color);
<<<<<<< HEAD
            width: 12px;
            text-align: center;
            flex-shrink: 0;
            font-size: 0.75rem;
=======
            width: 16px;
            text-align: center;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .detail-label {
            color: var(--text-muted);
<<<<<<< HEAD
            font-size: 0.65rem;
            display: block;
=======
            font-size: 0.85rem;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .detail-value {
            color: var(--text-color);
            font-weight: 600;
<<<<<<< HEAD
            font-size: 0.8rem;
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        }
        
        .mission-actions {
            display: flex;
            justify-content: center;
<<<<<<< HEAD
            gap: 8px;
            margin-top: auto;
            padding-top: 10px;
=======
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            border-top: 1px solid var(--border-color);
        }
        
        .btn-icon {
<<<<<<< HEAD
            width: 36px;
            height: 36px;
=======
            width: 45px;
            height: 45px;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
<<<<<<< HEAD
            font-size: 0.95rem;
=======
            font-size: 1.1rem;
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
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
<<<<<<< HEAD
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
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- En-tête avec statistiques -->
    <div class="missions-header">
        <h2 class="text-white">🎯 Gestion des Missions</h2>
        <a href="addmission.php" class="add-mission-btn">
            <i class="fas fa-plus"></i> Nouvelle Mission
        </a>
    </div>

    <!-- Statistiques des missions -->
<<<<<<< HEAD
        <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= count($allMissions) ?></span>
=======
    <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= count($missions) ?></span>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            <span class="mission-stat-label">Total Missions</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
<<<<<<< HEAD
                <?= count(array_filter($allMissions, fn($m) => strtolower($m['niveau_difficulte']) === 'facile')) ?>
=======
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'facile')) ?>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            </span>
            <span class="mission-stat-label">Missions Faciles</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
<<<<<<< HEAD
                <?= count(array_filter($allMissions, fn($m) => strtolower($m['niveau_difficulte']) === 'moyen')) ?>
=======
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'moyen')) ?>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            </span>
            <span class="mission-stat-label">Missions Moyennes</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
<<<<<<< HEAD
                <?= count(array_filter($allMissions, fn($m) => strtolower($m['niveau_difficulte']) === 'difficile')) ?>
=======
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'difficile')) ?>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
            </span>
            <span class="mission-stat-label">Missions Difficiles</span>
        </div>
    </div>

    <!-- Grille des missions -->
    <div class="mission-grid">
        <?php if (empty($missions)): ?>
            <div class="empty-state">
                <i class="fas fa-tasks"></i>
                <h3>Aucune mission trouvée</h3>
                <p>Commencez par créer votre première mission</p>
                <a href="addmission.php" class="add-mission-btn" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Créer une mission
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($missions as $m): ?>
                <div class="mission-card">
                    <!-- En-tête de la mission -->
                    <div class="mission-header">
<<<<<<< HEAD
                        <div>
                            <h3 class="mission-title"><?= htmlspecialchars($m['titre']) ?></h3>
                            <span class="mission-difficulty difficulty-<?= strtolower($m['niveau_difficulte']) ?>">
                                <?= ucfirst($m['niveau_difficulte']) ?>
                            </span>
                        </div>
=======
                        <h3 class="mission-title"><?= htmlspecialchars($m['titre']) ?></h3>
                        <span class="mission-difficulty difficulty-<?= $m['niveau_difficulte'] ?>">
                            <?= ucfirst($m['niveau_difficulte']) ?>
                        </span>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
                    </div>

                    <!-- Dates -->
                    <div class="mission-dates">
                        <div class="date-info">
<<<<<<< HEAD
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= date('d/m/Y', strtotime($m['date_debut'])) ?> - <?= date('d/m/Y', strtotime($m['date_fin'])) ?></span>
=======
                            <i class="fas fa-play-circle"></i>
                            Début: <?= date('d/m/Y', strtotime($m['date_debut'])) ?>
                        </div>
                        <div class="date-info">
                            <i class="fas fa-flag-checkered"></i>
                            Fin: <?= date('d/m/Y', strtotime($m['date_fin'])) ?>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
                        </div>
                    </div>

                    <!-- Détails de la mission -->
                    <div class="mission-details">
                        <div class="detail-item">
                            <i class="fas fa-gamepad"></i>
                            <div>
                                <div class="detail-label">Jeu</div>
                                <div class="detail-value"><?= htmlspecialchars($m['jeu']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-tags"></i>
                            <div>
                                <div class="detail-label">Thème</div>
                                <div class="detail-value"><?= htmlspecialchars($m['theme']) ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($m['description'])): ?>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <div class="detail-label">Description</div>
<<<<<<< HEAD
                                <div class="detail-value" style="font-weight: normal; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars(substr($m['description'], 0, 50)) ?>
                                    <?= strlen($m['description']) > 50 ? '...' : '' ?>
=======
                                <div class="detail-value" style="font-weight: normal;">
                                    <?= htmlspecialchars(substr($m['description'], 0, 100)) ?>
                                    <?= strlen($m['description']) > 100 ? '...' : '' ?>
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

<<<<<<< HEAD
                    <!-- Nombre de likes (style Facebook) -->
                    <div class="mission-likes-info" style="margin: 12px 0; padding: 10px; background: rgba(255, 74, 87, 0.1); border-radius: 8px; border-left: 3px solid #ff4a57; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="color: #ff4a57; font-size: 1rem;"></i>
                        <span style="color: var(--text-color); font-weight: 600; font-size: 0.9rem;">
                            <?= isset($likesCount[$m['id']]) ? number_format($likesCount[$m['id']], 0, ',', ' ') : '0' ?> 
                            <?= isset($likesCount[$m['id']]) && $likesCount[$m['id']] > 1 ? 'likes' : 'like' ?>
                        </span>
                    </div>

=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
                    <!-- Boutons d'action -->
                    <div class="mission-actions">
                        <!-- Candidatures -->
                        <a href="../condidature/listecondidature.php?mission_id=<?= $m['id'] ?>&mission_titre=<?= urlencode($m['titre']) ?>"
                           class="btn-icon btn-candidatures"
                           title="Voir les candidatures">
                            <i class="fas fa-users"></i>
                        </a>

                        <!-- Modifier -->
                        <a href="modifiermission.php?id=<?= $m['id'] ?>" 
                           class="btn-icon btn-modifier"
                           title="Modifier la mission">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Supprimer -->
                        <a href="deletemission.php?id=<?= $m['id'] ?>" 
                           class="btn-icon btn-supprimer"
                           title="Supprimer la mission"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<<<<<<< HEAD

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
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
</div>

</body>
</html>