<?php
require_once __DIR__ . '/../../../db_config.php';
require_once __DIR__ . '/../../../db_migrations.php';

$db = config::getConnexion();

// Get event ID from URL
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if (!$event_id) {
    header('Location: ../events/index.php');
    exit();
}

// Get event info
$eventStmt = $db->prepare("SELECT id_evenement, titre, description FROM evenement WHERE id_evenement = :id");
$eventStmt->execute(['id' => $event_id]);
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: ../events/index.php');
    exit();
}

// Get all feedback for this event
$feedbackStmt = $db->prepare("
    SELECT f.id, f.rating, f.commentaire, f.date_feedback, 
           u.id_util, u.prenom, u.nom, u.img
    FROM event_feedback f
    JOIN utilisateur u ON u.id_util = f.id_utilisateur
    WHERE f.id_event = :event_id
    ORDER BY f.date_feedback DESC
");
$feedbackStmt->execute(['event_id' => $event_id]);
$feedbacks = $feedbackStmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$totalFeedback = count($feedbacks);
$averageRating = 0;
if ($totalFeedback > 0) {
    $sumRating = 0;
    foreach ($feedbacks as $fb) {
        if ($fb['rating']) {
            $sumRating += $fb['rating'];
        }
    }
    $averageRating = round($sumRating / $totalFeedback, 1);
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $fb_id = intval($_GET['delete_id']);
    $deleteStmt = $db->prepare("DELETE FROM event_feedback WHERE id = :id");
    if ($deleteStmt->execute(['id' => $fb_id])) {
        header("Location: event_feedback.php?event_id=$event_id&success=1");
        exit();
    } else {
        header("Location: event_feedback.php?event_id=$event_id&error=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Feedback Événement - ENGAGE Admin</title>
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .feedbacks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .event-info {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }
        
        .event-info h3 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
        }
        
        .event-info p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.95rem;
        }
        
        .feedbacks-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .feedback-stat-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .feedback-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .feedback-stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .feedback-stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .feedback-card {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            margin-bottom: 20px;
        }
        
        .feedback-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .feedback-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feedback-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            overflow: hidden;
        }
        
        .feedback-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .feedback-user-info h4 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.1rem;
        }
        
        .feedback-user-info p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .feedback-rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .feedback-stars {
            color: #ffd700;
            font-size: 1.1rem;
        }
        
        .feedback-rating-number {
            background: var(--secondary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffd700;
            border: 1px solid #ffd700;
        }
        
        .feedback-comment {
            background: var(--secondary-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .feedback-comment p {
            color: var(--text-color);
            margin: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        .feedback-date {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-align: right;
        }
        
        .feedback-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .btn-icon:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--text-muted);
            opacity: 0.5;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: #28a745;
            color: #28a745;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
            color: #dc3545;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- En-tête -->
    <div class="feedbacks-header">
        <h2 class="text-white">💬 Feedback Événement</h2>
        <div>
            <a href="index.php" class="btn btn-primary" style="margin-right: 10px;">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Événements
            </a>
        </div>
    </div>

    <!-- Info événement -->
    <div class="event-info">
        <h3><i class="fas fa-calendar-alt me-2"></i><?= htmlspecialchars($event['titre']) ?></h3>
        <p><?= htmlspecialchars(substr($event['description'] ?? '', 0, 150)) ?>...</p>
    </div>

    <!-- Messages d'alerte -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>Feedback supprimé avec succès !
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la suppression !
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="feedbacks-stats">
        <div class="feedback-stat-card">
            <span class="feedback-stat-number"><?= $totalFeedback ?></span>
            <span class="feedback-stat-label">Total Avis</span>
        </div>
        <div class="feedback-stat-card">
            <span class="feedback-stat-number"><?= $averageRating ?></span>
            <span class="feedback-stat-label">Note Moyenne</span>
        </div>
    </div>

    <!-- Feedback List -->
    <?php if (empty($feedbacks)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3 class="text-white">Aucun avis pour le moment</h3>
            <p>Les participants n'ont pas encore laissé d'avis sur cet événement.</p>
        </div>
    <?php else: ?>
        <?php foreach ($feedbacks as $feedback): ?>
            <div class="feedback-card">
                <div class="feedback-header">
                    <div class="feedback-user">
                        <div class="feedback-avatar">
                            <?php if (!empty($feedback['img']) && file_exists(__DIR__ . '/../../../view/frontoffice/assets/uploads/profiles/' . $feedback['img'])): ?>
                                <img src="../../frontoffice/assets/uploads/profiles/<?= htmlspecialchars($feedback['img']) ?>" alt="Avatar">
                            <?php else: ?>
                                <?= strtoupper(substr($feedback['prenom'] ?? 'U', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="feedback-user-info">
                            <h4><?= htmlspecialchars($feedback['prenom'] ?? '') ?> <?= htmlspecialchars($feedback['nom'] ?? '') ?></h4>
                            <p><?= isset($feedback['date_feedback']) ? date('d/m/Y H:i', strtotime($feedback['date_feedback'])) : 'Date inconnue' ?></p>
                        </div>
                    </div>
                    <div class="feedback-rating">
                        <span class="feedback-stars">
                            <?php for ($i = 0; $i < ($feedback['rating'] ?? 0); $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="feedback-rating-number"><?= htmlspecialchars($feedback['rating'] ?? '--') ?>/5</span>
                    </div>
                </div>

                <?php if (!empty($feedback['commentaire'])): ?>
                    <div class="feedback-comment">
                        <p><?= nl2br(htmlspecialchars($feedback['commentaire'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="feedback-actions">
                    <button class="btn-icon btn-supprimer" 
                            onclick="if(confirm('Supprimer cet avis ?')) { window.location.href='event_feedback.php?event_id=<?= $event_id ?>&delete_id=<?= $feedback['id'] ?>'; }">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>

</html>
