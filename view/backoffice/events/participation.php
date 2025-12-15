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

$eventModel = new EvenementModel();
$participationModel = new ParticipationModel();

// Get event ID from URL
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    $_SESSION['error'] = "Événement non spécifié";
    header('Location: evenement.php');
    exit;
}

// Get event data
$event = $eventModel->getById($event_id);

if (!$event) {
    $_SESSION['error'] = "Événement non trouvé";
    header('Location: evenement.php');
    exit;
}

// Handle status update
if (isset($_POST['action']) && $_POST['action'] == 'update_status' && isset($_POST['participation_id'])) {
    $participation_id = $_POST['participation_id'];
    $status = $_POST['status'] ?? 'en attente';
    
    if ($participationModel->updateStatus($participation_id, $status)) {
        $_SESSION['success'] = "Statut de participation mis à jour!";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du statut";
    }
    header('Location: participation.php?event_id=' . $event_id);
    exit;
}

// Get all participations for this event
$allParticipations = $participationModel->getEventParticipations($event_id);

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6; // afficher 6 participations par page
$totalParticipations = count($allParticipations);
$totalPages = (int) ceil($totalParticipations / $perPage);
$offset = ($page - 1) * $perPage;
$participations = array_slice($allParticipations, $offset, $perPage);

$pageTitle = "Participations pour : " . htmlspecialchars($event['titre']);

// Vérifier si l'export CSV est demandé
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=participations_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // En-têtes du CSV
    fputcsv($output, ['ID', 'Événement', 'Nom', 'Email', 'Gamer Tag', 'Quantité', 'Montant', 'Mode Paiement', 'Référence', 'Statut', 'Date de participation']);
    
    foreach ($allParticipations as $p) {
        fputcsv($output, [
            $p['id_participation'],
            html_entity_decode($event['titre']),
            html_entity_decode($p['prenom'] . ' ' . $p['nom']),
            $p['email'],
            html_entity_decode($p['gamer_tag'] ?? ''),
            $p['quantite'] ?? 1,
            $p['montant_total'] ?? 0,
            html_entity_decode($p['mode_paiement'] ?? ''),
            html_entity_decode($p['reference_paiement'] ?? ''),
            html_entity_decode($p['statut']),
            $p['date_participation']
        ]);
    }
    
    fclose($output);
    exit();
}
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
        
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-export {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-export:hover {
            background: linear-gradient(45deg, #20c997, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.4);
            color: white;
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
        
        .status-en_attente, .status-en-attente {
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
        
        .btn-view {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-view:hover {
            background: linear-gradient(45deg, #138496, #117a8b);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }
        
        .btn-accepter {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-accepter:hover {
            background: linear-gradient(45deg, #20c997, #17a2b8);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .btn-refuser {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-refuser:hover {
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
            
            .candidatures-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                margin-top: 15px;
            }
            
            .btn-export {
                width: 100%;
                justify-content: center;
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
                👥 Participations pour : <span style="color: var(--primary-color)">"<?= htmlspecialchars($event['titre']) ?>"</span>
            </h2>
            
            <a href="evenement.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour aux événements
            </a>
        </div>
        
        <div class="header-actions">
            <?php if (!empty($allParticipations)): ?>
                <a href="?event_id=<?= $event_id ?>&export=csv" class="btn-export">
                    <i class="fas fa-file-csv"></i> Exporter en CSV
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Statistiques des participations -->
    <div class="candidatures-stats">
        <div class="candidature-stat-card">
            <span class="candidature-stat-number"><?= count($allParticipations) ?></span>
            <span class="candidature-stat-label">Total Participations</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allParticipations, fn($p) => $p['statut'] === 'en attente')) ?>
            </span>
            <span class="candidature-stat-label">En attente</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allParticipations, fn($p) => $p['statut'] === 'acceptée' || $p['statut'] === 'acceptee')) ?>
            </span>
            <span class="candidature-stat-label">Acceptées</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?php 
                    $totalRevenu = 0;
                    foreach ($allParticipations as $p) {
                        if ($p['statut'] === 'acceptée' || $p['statut'] === 'acceptee') {
                            $totalRevenu += (float)($p['montant_total'] ?? 0);
                        }
                    }
                    echo number_format($totalRevenu, 2);
                ?> TND
            </span>
            <span class="candidature-stat-label">Revenu Total</span>
        </div>
    </div>

    <!-- Grille des participations -->
    <div class="candidature-grid">
        <?php if (empty($participations)): ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Aucune participation trouvée</h3>
                <p>Aucun participant n'a encore rejoint cet événement</p>
            </div>
        <?php else: ?>
            <?php foreach ($participations as $p): ?>
                <div class="candidature-card">
                    <!-- En-tête de la participation -->
                    <div class="candidature-header-info">
                        <div class="candidature-id">#<?= $p['id_participation'] ?></div>
                        <h3 class="candidature-pseudo">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                        </h3>
                        <?php if (!empty($p['gamer_tag'])): ?>
                            <span class="candidature-mission">
                                <i class="fas fa-gamepad"></i> @<?= htmlspecialchars($p['gamer_tag']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Détails de la participation -->
                    <div class="candidature-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?= htmlspecialchars($p['email']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <div class="detail-label">Date participation</div>
                                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($p['date_participation'])) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <div class="detail-label">Quantité</div>
                                <div class="detail-value"><?= $p['quantite'] ?? 1 ?> ticket(s)</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <div class="detail-label">Montant</div>
                                <div class="detail-value">
                                    <?php if (!empty($p['montant_total']) && $p['montant_total'] > 0): ?>
                                        <?= number_format($p['montant_total'], 2) ?> TND
                                    <?php else: ?>
                                        Gratuit
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($p['mode_paiement'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-credit-card"></i>
                            <div>
                                <div class="detail-label">Mode paiement</div>
                                <div class="detail-value"><?= htmlspecialchars($p['mode_paiement']) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="detail-item">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <div class="detail-label">Statut</div>
                                <div class="detail-value">
                                    <span class="candidature-status status-<?= strtolower(str_replace([' ', 'é'], ['-', 'e'], $p['statut'])) ?>">
                                        <?php
                                        switch($p['statut']) {
                                            case 'en attente':
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
                                                echo htmlspecialchars($p['statut']);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="candidature-actions">
                        <?php if ($p['statut'] == 'en attente'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="participation_id" value="<?= $p['id_participation'] ?>">
                                <input type="hidden" name="status" value="acceptée">
                                <button type="submit" class="btn-icon btn-accepter" title="Accepter">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="participation_id" value="<?= $p['id_participation'] ?>">
                                <input type="hidden" name="status" value="refusée">
                                <button type="submit" class="btn-icon btn-refuser" title="Refuser">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        <?php elseif ($p['statut'] == 'acceptée' || $p['statut'] == 'acceptee'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="participation_id" value="<?= $p['id_participation'] ?>">
                                <input type="hidden" name="status" value="refusée">
                                <button type="submit" class="btn-icon btn-refuser" title="Annuler/Refuser">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="participation_id" value="<?= $p['id_participation'] ?>">
                                <input type="hidden" name="status" value="acceptée">
                                <button type="submit" class="btn-icon btn-accepter" title="Réactiver">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        <?php endif; ?>
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
                    <a class="page-link" href="?event_id=<?= $event_id ?>&page=<?= max(1, $page-1) ?>" aria-label="Précédent">&laquo;</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?event_id=<?= $event_id ?>&page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?event_id=<?= $event_id ?>&page=<?= min($totalPages, $page+1) ?>" aria-label="Suivant">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

</body>
</html>