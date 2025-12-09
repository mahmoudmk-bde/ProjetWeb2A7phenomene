<?php 
require_once __DIR__ . '/../../../controller/missioncontroller.php';
$missionC = new missioncontroller();
$missions = $missionC->missionliste();
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
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .mission-header {
            margin: 0;
            flex: 1;

        }
        
        .mission-difficulty {
            background: var(--secondary-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 15px;
        }
        
        .difficulty-facile { color: #28a745; border: 1px solid #28a745; }
        .difficulty-moyen { color: #ffc107; border: 1px solid #ffc107; }
        .difficulty-difficile { color: #dc3545; border: 1px solid #dc3545; }
        
        .mission-dates {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .date-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .date-info i {
            color: var(--primary-color);
        }
        
        .mission-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--secondary-color);
            border-radius: 8px;
        }
        
        .detail-item i {
            color: var(--primary-color);
            width: 16px;
            text-align: center;
        }
        
        .detail-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .detail-value {
            color: var(--text-color);
            font-weight: 600;
        }
        
        .mission-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
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
    <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= count($missions) ?></span>
            <span class="mission-stat-label">Total Missions</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'facile')) ?>
            </span>
            <span class="mission-stat-label">Missions Faciles</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'moyen')) ?>
            </span>
            <span class="mission-stat-label">Missions Moyennes</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($missions, fn($m) => $m['niveau_difficulte'] === 'difficile')) ?>
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
                        <h3 class="mission-title"><?= htmlspecialchars($m['titre']) ?></h3>
                        <span class="mission-difficulty difficulty-<?= $m['niveau_difficulte'] ?>">
                            <?= ucfirst($m['niveau_difficulte']) ?>
                        </span>
                    </div>

                    <!-- Dates -->
                    <div class="mission-dates">
                        <div class="date-info">
                            <i class="fas fa-play-circle"></i>
                            Début: <?= date('d/m/Y', strtotime($m['date_debut'])) ?>
                        </div>
                        <div class="date-info">
                            <i class="fas fa-flag-checkered"></i>
                            Fin: <?= date('d/m/Y', strtotime($m['date_fin'])) ?>
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
                                <div class="detail-value" style="font-weight: normal;">
                                    <?= htmlspecialchars(substr($m['description'], 0, 100)) ?>
                                    <?= strlen($m['description']) > 100 ? '...' : '' ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

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
</div>

</body>
</html>