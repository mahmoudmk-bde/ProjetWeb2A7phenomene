<?php
require_once __DIR__ . '/../../../controller/feedbackcontroller.php';
$feedbackcontroller = new feedbackcontroller();

$feedbacks = $feedbackcontroller->getAllFeedbacks();

// Traitement de la suppression
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($feedbackcontroller->deleteFeedback($id)) {
        header("Location: feedbackliste.php?success=1");
        exit();
    } else {
        header("Location: feedbackliste.php?error=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Feedback - ENGAGE Admin</title>

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
        
        .feedback-mission {
            background: var(--secondary-color);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .feedback-mission h5 {
            color: var(--text-color);
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        .feedback-mission p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
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

    <!-- En-tête avec statistiques -->
    <div class="feedbacks-header">
        <h2 class="text-white">💬 Gestion des Feedback</h2>
        <div>
            <a href="missionliste.php" class="btn btn-primary" style="margin-right: 10px;">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Missions
            </a>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>Feedback supprimé avec succès !
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la suppression du feedback !
        </div>
    <?php endif; ?>

    <!-- Statistiques des feedbacks -->
    <div class="feedbacks-stats">
        <div class="feedback-stat-card">
            <span class="feedback-stat-number"><?= count($feedbacks) ?></span>
            <span class="feedback-stat-label">Total Feedback</span>
        </div>
        <div class="feedback-stat-card">
            <span class="feedback-stat-number">
                <?= $feedbackcontroller->getPlatformAverageRating() ?>
            </span>
            <span class="feedback-stat-label">Note Moyenne</span>
        </div>
        <div class="feedback-stat-card">
            <span class="feedback-stat-number">
                <?= count(array_filter($feedbacks, fn($f) => $f['rating'] == 5)) ?>
            </span>
            <span class="feedback-stat-label">5 Étoiles</span>
        </div>
        <div class="feedback-stat-card">
            <span class="feedback-stat-number">
                <?= count(array_filter($feedbacks, fn($f) => $f['rating'] <= 2)) ?>
            </span>
            <span class="feedback-stat-label">Notes Faibles</span>
        </div>
    </div>

    <!-- Liste des feedbacks -->
    <?php if (empty($feedbacks)): ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>Aucun feedback trouvé</h3>
            <p>Les feedbacks des utilisateurs apparaîtront ici.</p>
        </div>
    <?php else: ?>
        <div class="feedbacks-list">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-card">
                    <!-- En-tête du feedback -->
                    <div class="feedback-header">
                        <div class="feedback-user">
                            <div class="feedback-avatar">
                                <?= strtoupper(substr($feedback['prenom'], 0, 1) . substr($feedback['nom'], 0, 1)) ?>
                            </div>
                            <div class="feedback-user-info">
                                <h4><?= htmlspecialchars($feedback['prenom'] . ' ' . $feedback['nom']) ?></h4>
                                <p><?= htmlspecialchars($feedback['mail']) ?></p>
                            </div>
                        </div>
                        
                        <div class="feedback-rating">
                            <div class="feedback-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= $feedback['rating'] ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="feedback-rating-number"><?= $feedback['rating'] ?>/5</span>
                        </div>
                    </div>

                    <!-- Mission concernée -->
                    <div class="feedback-mission">
                        <h5>📋 Mission: <?= htmlspecialchars($feedback['mission_titre']) ?></h5>
                        <p>ID Mission: #<?= $feedback['id_mission'] ?></p>
                    </div>

                    <!-- Commentaire -->
                    <?php if (!empty($feedback['commentaire'])): ?>
                        <div class="feedback-comment">
                            <p>"<?= nl2br(htmlspecialchars($feedback['commentaire'])) ?>"</p>
                        </div>
                    <?php endif; ?>

                    <!-- Date -->
                    <div class="feedback-date">
                        <i class="fas fa-calendar me-2"></i>
                        Posté le <?= date('d/m/Y à H:i', strtotime($feedback['date_feedback'])) ?>
                    </div>

                    <!-- Actions -->
                    <div class="feedback-actions">
                        <a href="feedbackliste.php?delete_id=<?= $feedback['id'] ?>" 
                           class="btn-icon btn-supprimer"
                           title="Supprimer ce feedback"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce feedback ? Cette action est irréversible.');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Confirmation de suppression
document.querySelectorAll('.btn-supprimer').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce feedback ? Cette action est irréversible.')) {
            e.preventDefault();
        }
    });
});

// Auto-dissimulation des alertes
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

</body>
</html>