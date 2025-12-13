<?php
require_once __DIR__ . '/../../../controller/condidaturecontroller.php';
require_once __DIR__ . '/../../../db_config.php';

$condC = new condidaturecontroller();

// Vérifier si la colonne 'cv' existe dans la table candidatures
$hasCv = false;
try {
    $db = config::getConnexion();
    $colsStmt = $db->query("SHOW COLUMNS FROM candidatures");
    $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $hasCv = in_array('cv', $cols, true);
} catch (Exception $e) {
    $hasCv = false;
}

// Vérifier si un filtre par mission est demandé
$mission_id = $_GET['mission_id'] ?? null;

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6; // afficher 6 candidatures par page

if ($mission_id) {
    $pag = $condC->getCondidaturesPaginated($page, $perPage, $mission_id);
    $mission_titre = $condC->getMissionTitle($mission_id);
    $pageTitle = "Candidatures pour : " . htmlspecialchars($mission_titre);
    $isFiltered = true;
} else {
    $pag = $condC->getCondidaturesPaginated($page, $perPage);
    $pageTitle = "Toutes les candidatures";
    $isFiltered = false;
}

$candidatures = $pag['data'];
$totalCandidatures = $pag['total'];
$totalPages = (int) ceil($totalCandidatures / $perPage);

// Garder toutes les candidatures pour les stats
$allCandidatures = $condC->getAllCondidatures();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> - ENGAGE Admin</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .candidatures-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .candidatures-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .candidature-stat-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .candidature-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .candidature-stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .candidature-stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .candidature-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .candidature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .candidature-header-info {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .candidature-id {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        
        .candidature-pseudo {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .candidature-mission {
            background: var(--secondary-color);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            color: var(--text-color);
        }
        
        .candidature-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
            flex: 1;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: var(--secondary-color);
            border-radius: 6px;
            font-size: 0.8rem;
        }
        
        .detail-item i {
            color: var(--primary-color);
            width: 16px;
            text-align: center;
            flex-shrink: 0;
            font-size: 0.85rem;
        }
        
        .detail-label {
            color: var(--text-muted);
            font-size: 0.7rem;
            display: block;
        }
        
        .detail-value {
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .candidature-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .status-en_attente {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        
        .status-acceptée, .status-acceptee {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .status-refusée, .status-refusee {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        .candidature-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 12px;
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
        
        .btn-cv-view {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-cv-view:hover {
            background: linear-gradient(45deg, #138496, #117a8b);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }
        
        .btn-cv-download {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-cv-download:hover {
            background: linear-gradient(45deg, #5a6268, #495057);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
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
        
        .candidature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .candidature-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .candidature-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        .back-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
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
    <div class="candidatures-header">
        <div>
            <h2 class="text-white">
                <?php if ($isFiltered): ?>
                    📋 Candidatures pour : <span style="color: var(--primary-color)">"<?= htmlspecialchars($mission_titre) ?>"</span>
                <?php else: ?>
                    📋 Gestion des Candidatures
                <?php endif; ?>
            </h2>
            
            <?php if ($isFiltered): ?>
                <a href="listecondidature.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voir toutes les candidatures
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistiques des candidatures -->
    <div class="candidatures-stats">
        <div class="candidature-stat-card">
            <span class="candidature-stat-number"><?= count($allCandidatures) ?></span>
            <span class="candidature-stat-label">Total Candidatures</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allCandidatures, fn($c) => $c['statut'] === 'en_attente')) ?>
            </span>
            <span class="candidature-stat-label">En attente</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allCandidatures, fn($c) => $c['statut'] === 'acceptée' || $c['statut'] === 'acceptee')) ?>
            </span>
            <span class="candidature-stat-label">Acceptées</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allCandidatures, fn($c) => $c['statut'] === 'refusée' || $c['statut'] === 'refusee')) ?>
            </span>
            <span class="candidature-stat-label">Refusées</span>
        </div>
    </div>

    <!-- Grille des candidatures -->
    <div class="candidature-grid">
        <?php if (empty($candidatures)): ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Aucune candidature trouvée</h3>
                <p><?= $isFiltered ? 'Aucun joueur n\'a encore postulé à cette mission' : 'Aucun joueur n\'a encore postulé aux missions' ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($candidatures as $c): ?>
                <div class="candidature-card">
                    <!-- En-tête de la candidature -->
                    <div class="candidature-header-info">
                        <div class="candidature-id">#<?= $c['id'] ?></div>
                        <h3 class="candidature-pseudo">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($c['pseudo_gaming']) ?>
                        </h3>
                        <span class="candidature-mission">
                            <i class="fas fa-tasks"></i> <?= htmlspecialchars($c['mission_titre']) ?>
                        </span>
                    </div>

                    <!-- Détails de la candidature -->
                    <div class="candidature-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?= htmlspecialchars($c['email']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <div class="detail-label">Niveau d'expérience</div>
                                <div class="detail-value"><?= htmlspecialchars($c['niveau_experience']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <div class="detail-label">Disponibilités</div>
                                <div class="detail-value"><?= htmlspecialchars($c['disponibilites']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <div class="detail-label">Statut</div>
                                <div class="detail-value">
                                    <span class="candidature-status status-<?= strtolower(str_replace('é', 'e', $c['statut'])) ?>">
                                        <?php
                                        switch($c['statut']) {
                                            case 'en_attente':
                                                echo '⏳ En Attente';
                                                break;
                                            case 'acceptée':
                                            case 'acceptee':
                                                echo '✅ Acceptée';
                                                break;
                                            case 'refusée':
                                            case 'refusee':
                                                echo '❌ Refusée';
                                                break;
                                            default:
                                                echo htmlspecialchars($c['statut']);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="candidature-actions">
                        <?php if ($hasCv && !empty($c['cv'])): ?>
                            <?php
                            // Calculer le chemin relatif vers cv.php depuis la racine du projet
                            $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
                            $viewDir = str_replace('\\', '/', realpath(__DIR__ . '/../../../'));
                            $projectWebRoot = str_replace($docRoot, '', $viewDir);
                            $projectWebRoot = '/' . trim($projectWebRoot, '/');
                            if ($projectWebRoot === '/') { $projectWebRoot = ''; }
                            // Utiliser l'endpoint CV pour affichage/téléchargement sûr
                            $cv_view_link = '/' . ltrim($projectWebRoot, '/') . '/cv.php?id=' . urlencode($c['id']) . '&mode=view';
                            $cv_download_link = '/' . ltrim($projectWebRoot, '/') . '/cv.php?id=' . urlencode($c['id']) . '&mode=download';
                            ?>
                            <a href="<?= htmlspecialchars($cv_view_link) ?>" target="_blank" rel="noopener noreferrer" 
                               class="btn-icon btn-cv-view" title="Voir le CV">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= htmlspecialchars($cv_download_link) ?>" 
                               class="btn-icon btn-cv-download" title="Télécharger le CV">
                                <i class="fas fa-download"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="modifiercondidature.php?id=<?= $c['id'] ?>" 
                           class="btn-icon btn-modifier"
                           title="Modifier statut">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="deletecondidature.php?id=<?= $c['id'] ?>" 
                           class="btn-icon btn-supprimer"
                           title="Supprimer"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?');">
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
                    <a class="page-link" href="?<?= $mission_id ? 'mission_id=' . $mission_id . '&' : '' ?>page=<?= max(1, $page-1) ?>" aria-label="Précédent">&laquo;</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $mission_id ? 'mission_id=' . $mission_id . '&' : '' ?>page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= $mission_id ? 'mission_id=' . $mission_id . '&' : '' ?>page=<?= min($totalPages, $page+1) ?>" aria-label="Suivant">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script src="../assets/js/back.js"></script>

</body>
</html>
