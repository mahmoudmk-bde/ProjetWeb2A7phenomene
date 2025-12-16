<?php
require_once __DIR__ . '/../../../db_config.php';
require_once __DIR__ . '/../../../db_migrations.php';

$db = config::getConnexion();

// Get all events with feedback count
$eventsStmt = $db->query("
    SELECT e.id_evenement, e.titre, e.date_evenement, e.lieu,
           COUNT(DISTINCT p.id_participation) as total_participants,
           COUNT(DISTINCT f.id) as total_feedback,
           AVG(f.rating) as avg_rating
    FROM evenement e
    LEFT JOIN participation p ON p.id_evenement = e.id_evenement
    LEFT JOIN event_feedback f ON f.id_event = e.id_evenement
    GROUP BY e.id_evenement, e.titre, e.date_evenement, e.lieu
    ORDER BY e.date_evenement DESC
");
$events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Avis Événements - ENGAGE Admin</title>
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .event-card {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .event-title {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .event-meta {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .event-meta i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .event-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .rating-badge {
            display: inline-block;
            background: #ffd700;
            color: #333;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .no-feedback {
            color: var(--text-muted);
            font-style: italic;
        }
        
        .view-feedback-btn {
            width: 100%;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .view-feedback-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- En-tête -->
    <div class="events-header">
        <h2 class="text-white">💬 Avis Événements</h2>
    </div>

    <!-- Events Grid -->
    <?php if (empty($events)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3 class="text-white">Aucun événement</h3>
            <p>Il n'y a aucun événement pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="event-title">
                        <?= htmlspecialchars($event['titre']) ?>
                    </div>
                    
                    <div class="event-meta">
                        <span>
                            <i class="fas fa-calendar-alt"></i>
                            <?= !empty($event['date_evenement']) ? date('d/m/Y', strtotime($event['date_evenement'])) : 'Date inconnue' ?>
                        </span>
                        <span>
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($event['lieu'] ?? 'Lieu inconnue') ?>
                        </span>
                    </div>
                    
                    <div class="event-stats">
                        <div class="stat">
                            <span class="stat-number"><?= $event['total_participants'] ?? 0 ?></span>
                            <span class="stat-label">Participants</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number"><?= $event['total_feedback'] ?? 0 ?></span>
                            <span class="stat-label">Avis</span>
                        </div>
                        <div class="stat">
                            <?php if ($event['total_feedback'] > 0): ?>
                                <span class="stat-number"><?= number_format($event['avg_rating'] ?? 0, 1) ?></span>
                                <span class="stat-label">Note</span>
                            <?php else: ?>
                                <span class="stat-label">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button class="view-feedback-btn" onclick="window.parent.openPage('events/event_feedback.php?event_id=<?= $event['id_evenement'] ?>')">
                        <i class="fas fa-comments"></i>
                        Voir les avis
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>

</html>
