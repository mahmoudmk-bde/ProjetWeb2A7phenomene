<?php
// Use forward slashes which PHP handles correctly on Windows
// From view/backoffice/reclamation/ we need to go up 3 levels to reach project root
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';

// Run database migrations
require_once $base_dir . 'db_migrations.php';

require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$allReclamations = $recCtrl->listReclamations();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6; // afficher 6 réclamations par page
$totalReclamations = count($allReclamations);
$totalPages = (int) ceil($totalReclamations / $perPage);
$offset = ($page - 1) * $perPage;
$list = array_slice($allReclamations, $offset, $perPage);

// Message de succès après suppression
$successMessage = isset($_GET['deleted']) && $_GET['deleted'] == 1 ? 'Réclamation supprimée avec succès !' : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réclamations - ENGAGE Admin</title>
    
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="../assets/css/tags.css">
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
        .difficulty-en_attente { color: #ffc107; border: 1px solid #ffc107; }
        .difficulty-traite { color: #28a745; border: 1px solid #28a745; }
        .difficulty-rejete { color: #dc3545; border: 1px solid #dc3545; }
        
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
        
        .btn-reply {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-reply:hover {
            background: linear-gradient(45deg, #138496, #117a8b);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }
        
        .btn-view {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-view:hover {
            background: linear-gradient(45deg, #0056b3, #004099);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
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

    <!-- En-tête -->
    <div class="missions-header">
        <h2 class="text-white">📋 Gestion des Réclamations</h2>
    </div>

    <!-- Message de succès -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- Statistiques des réclamations -->
    <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= count($allReclamations) ?></span>
            <span class="mission-stat-label">Total Réclamations</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($allReclamations, fn($r) => $r['statut'] === 'Non traite')) ?>
            </span>
            <span class="mission-stat-label">Non traitées</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($allReclamations, fn($r) => $r['statut'] === 'En cours')) ?>
            </span>
            <span class="mission-stat-label">En cours</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number">
                <?= count(array_filter($allReclamations, fn($r) => $r['statut'] === 'Traite')) ?>
            </span>
            <span class="mission-stat-label">Traitées</span>
        </div>
    </div>

    <!-- Grille des réclamations -->
    <div class="mission-grid">
        <?php if (empty($list)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucune réclamation trouvée</h3>
                <p>Les réclamations apparaîtront ici dès que les utilisateurs en soumettront.</p>
            </div>
        <?php else: ?>
            <?php foreach ($list as $rec): 
                $responses = $respCtrl->getResponses($rec['id']);
                $countResp = $responses ? count($responses) : 0;
                $tags = $recCtrl->getTagsByReclamation((int)$rec['id']);
                $tagsStr = htmlspecialchars(implode(',', $tags));
            ?>
                <div class="mission-card" data-reclamation-item data-tags="<?= $tagsStr ?>">
                    <!-- En-tête de la réclamation -->
                    <div class="mission-header">
                        <div>
                            <h3 class="mission-title"><?= htmlspecialchars($rec['sujet']) ?></h3>
                            <?php
                                $statusClass = 'difficulty-en_attente';
                                $statusLabel = 'Non traité';
                                if ($rec['statut'] === 'En cours') {
                                    $statusClass = 'difficulty-moyen';
                                    $statusLabel = 'En cours';
                                } elseif ($rec['statut'] === 'Traite') {
                                    $statusClass = 'difficulty-traite';
                                    $statusLabel = 'Traité';
                                }
                            ?>
                            <span class="mission-difficulty <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </span>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="mission-dates">
                        <div class="date-info">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= date('d/m/Y H:i', strtotime($rec['date_creation'])) ?></span>
                        </div>
                    </div>

                    <!-- Détails de la réclamation -->
                    <div class="mission-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?= htmlspecialchars($rec['email']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-comments"></i>
                            <div>
                                <div class="detail-label">Réponses</div>
                                <div class="detail-value"><?= $countResp ?> réponse<?= $countResp !== 1 ? 's' : '' ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($rec['description'])): ?>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <div class="detail-label">Description</div>
                                <div class="detail-value" style="font-weight: normal; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars(substr($rec['description'], 0, 50)) ?>
                                    <?= strlen($rec['description']) > 50 ? '...' : '' ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Priorité info -->
                    <div class="mission-likes-info" style="margin: 12px 0; padding: 10px; background: rgba(255, 74, 87, 0.1); border-radius: 8px; border-left: 3px solid #ff4a57; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-flag" style="color: #ff4a57; font-size: 1rem;"></i>
                        <span style="color: var(--text-color); font-weight: 600; font-size: 0.9rem;">
                            Priorité: <?= htmlspecialchars($rec['priorite'] ?? 'Normal') ?>
                        </span>
                    </div>

                    <!-- Tags -->
                    <div style="margin: 12px 0;">
                        <?php $reclamationId = (int)$rec['id']; include 'tags_partial.php'; ?>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mission-actions">
                        <!-- Répondre -->
                        <a href="response.php?id=<?= $rec['id'] ?>" 
                           class="btn-icon btn-reply"
                           title="Répondre">
                            <i class="fas fa-reply"></i>
                        </a>

                        <!-- Voir détails -->
                        <a href="details.php?id=<?= $rec['id'] ?>" 
                           class="btn-icon btn-view"
                           title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Supprimer -->
                        <a href="delete.php?id=<?= $rec['id'] ?>" 
                           class="btn-icon btn-supprimer"
                           title="Supprimer"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
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

<script src="../assets/js/tags.js"></script>
</body>
</html>
