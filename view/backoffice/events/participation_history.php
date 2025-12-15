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

$participationModel = new ParticipationModel();
$allHistory = $participationModel->getAllParticipationsWithUsers();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6; // afficher 6 participations par page
$totalHistory = count($allHistory);
$totalPages = (int) ceil($totalHistory / $perPage);
$offset = ($page - 1) * $perPage;
$history = array_slice($allHistory, $offset, $perPage);

$pageTitle = "Historique Global des Participations";

// Vérifier si l'export CSV est demandé
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=historique_participations_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // En-têtes du CSV
    fputcsv($output, ['ID', 'Participant', 'Email', 'Événement', 'Date Événement', 'Date Participation', 'Quantité', 'Montant', 'Mode Paiement', 'Référence', 'Statut']);
    
    foreach ($allHistory as $row) {
        $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
        $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
            ? $row['montant_total']
            : ($row['type_evenement'] === 'payant'
                ? $qty * (float)($row['prix'] ?? 0)
                : 0);
        $modeDisplay = $row['mode_paiement'] ?? ($row['type_evenement'] === 'payant' ? 'Carte' : 'Gratuit');
        
        fputcsv($output, [
            $row['id_participation'],
            html_entity_decode($row['prenom'] . ' ' . $row['nom']),
            $row['email'],
            html_entity_decode($row['titre']),
            $row['date_evenement'] ?? '',
            $row['date_participation'],
            $qty,
            $amountDisplay,
            html_entity_decode($modeDisplay),
            html_entity_decode($row['reference_paiement'] ?? ''),
            html_entity_decode($row['statut'])
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
            <h2 class="text-white">📜 Historique Global des Participations</h2>
            
            <a href="evenement.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour aux événements
            </a>
        </div>
        
        <div class="header-actions">
            <?php if (!empty($allHistory)): ?>
                <a href="?export=csv" class="btn-export">
                    <i class="fas fa-file-csv"></i> Exporter en CSV
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistiques des participations -->
    <div class="candidatures-stats">
        <div class="candidature-stat-card">
            <span class="candidature-stat-number"><?= count($allHistory) ?></span>
            <span class="candidature-stat-label">Total Participations</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allHistory, fn($p) => $p['statut'] === 'en attente')) ?>
            </span>
            <span class="candidature-stat-label">En attente</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?= count(array_filter($allHistory, fn($p) => $p['statut'] === 'acceptée' || $p['statut'] === 'acceptee')) ?>
            </span>
            <span class="candidature-stat-label">Acceptées</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?php 
                    $totalRevenu = 0;
                    foreach ($allHistory as $p) {
                        if ($p['statut'] === 'acceptée' || $p['statut'] === 'acceptee') {
                            if (isset($p['montant_total']) && $p['montant_total'] !== null) {
                                $totalRevenu += (float)$p['montant_total'];
                            } elseif ($p['type_evenement'] === 'payant') {
                                $qty = isset($p['quantite']) ? max(1, (int)$p['quantite']) : 1;
                                $totalRevenu += $qty * (float)($p['prix'] ?? 0);
                            }
                        }
                    }
                    echo number_format($totalRevenu, 2);
                ?> TND
            </span>
            <span class="candidature-stat-label">Revenu Total</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?php
                    $uniqueEvents = array_unique(array_column($allHistory, 'id_evenement'));
                    echo count($uniqueEvents);
                ?>
            </span>
            <span class="candidature-stat-label">Événements Différents</span>
        </div>
        <div class="candidature-stat-card">
            <span class="candidature-stat-number">
                <?php
                    $uniqueUsers = [];
                    foreach ($allHistory as $p) {
                        $key = strtolower(trim($p['email']));
                        $uniqueUsers[$key] = true;
                    }
                    echo count($uniqueUsers);
                ?>
            </span>
            <span class="candidature-stat-label">Participants Uniques</span>
        </div>
    </div>

    <!-- Grille des participations -->
    <div class="candidature-grid">
        <?php if (empty($history)): ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h3>Aucun historique trouvé</h3>
                <p>Aucune participation n'a encore été enregistrée</p>
            </div>
        <?php else: ?>
            <?php foreach ($history as $row): 
                $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
                $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
                    ? number_format((float)$row['montant_total'], 2) . ' TND'
                    : ($row['type_evenement'] === 'payant'
                        ? number_format($qty * (float)($row['prix'] ?? 0), 2) . ' TND'
                        : 'Gratuit');
                $modeDisplay = $row['mode_paiement'] ?? ($row['type_evenement'] === 'payant' ? 'Carte' : 'Gratuit');
            ?>
                <div class="candidature-card">
                    <!-- En-tête de la participation -->
                    <div class="candidature-header-info">
                        <div class="candidature-id">#<?= $row['id_participation'] ?></div>
                        <h3 class="candidature-pseudo">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
                        </h3>
                        <span class="candidature-mission">
                            <i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($row['titre']) ?>
                        </span>
                    </div>

                    <!-- Détails de la participation -->
                    <div class="candidature-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?= htmlspecialchars($row['email']) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-calendar-day"></i>
                            <div>
                                <div class="detail-label">Date événement</div>
                                <div class="detail-value">
                                    <?= !empty($row['date_evenement']) ? date('d/m/Y', strtotime($row['date_evenement'])) : '-' ?>
                                    <?php if (!empty($row['heure_evenement'])): ?>
                                        à <?= substr($row['heure_evenement'],0,5) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <div class="detail-label">Date participation</div>
                                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($row['date_participation'])) ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <div class="detail-label">Quantité</div>
                                <div class="detail-value"><?= $qty ?> ticket(s)</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <div class="detail-label">Montant</div>
                                <div class="detail-value"><?= $amountDisplay ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <i class="fas fa-credit-card"></i>
                            <div>
                                <div class="detail-label">Mode paiement</div>
                                <div class="detail-value"><?= htmlspecialchars($modeDisplay) ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($row['reference_paiement'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-receipt"></i>
                            <div>
                                <div class="detail-label">Référence</div>
                                <div class="detail-value" style="font-size: 0.7rem;"><?= htmlspecialchars($row['reference_paiement']) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="detail-item">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <div class="detail-label">Statut</div>
                                <div class="detail-value">
                                    <span class="candidature-status status-<?= strtolower(str_replace([' ', 'é'], ['-', 'e'], $row['statut'])) ?>">
                                        <?php
                                        switch($row['statut']) {
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
                                                echo htmlspecialchars($row['statut']);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
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

</body>
</html>

