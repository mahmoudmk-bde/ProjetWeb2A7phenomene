<?php
// Use forward slashes which PHP handles correctly on Windows
// From view/backoffice/reclamation/ we need to go up 3 levels to reach project root
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';
require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$list = $recCtrl->listReclamations();

// Calcul des statistiques de base
$totalReclamations = count($list);
$nonTraitees = count(array_filter($list, function($r) { return $r['statut'] === 'Non traite'; }));
$traitees = count(array_filter($list, function($r) { return $r['statut'] === 'Traite'; }));
$enCours = count(array_filter($list, function($r) { return $r['statut'] === 'En cours'; }));

// Statistiques par priorité
$priorites = ['Basse' => 0, 'Moyenne' => 0, 'Haute' => 0, 'Urgente' => 0];
foreach ($list as $rec) {
    $priorite = $rec['priorite'] ?? 'Moyenne';
    if (isset($priorites[$priorite])) {
        $priorites[$priorite]++;
    }
}

// Statistiques par statut
$statuts = ['Non traite' => 0, 'En cours' => 0, 'Traite' => 0];
foreach ($list as $rec) {
    $statut = $rec['statut'] ?? 'Non traite';
    if (isset($statuts[$statut])) {
        $statuts[$statut]++;
    }
}

// Réclamations avec et sans réponses
$avecReponses = 0;
$sansReponses = 0;
foreach ($list as $rec) {
    $responses = $respCtrl->getResponses($rec['id']);
    if (count($responses) > 0) {
        $avecReponses++;
    } else {
        $sansReponses++;
    }
}

// Réclamations ce mois
$ceMois = 0;
$ceMoisDebut = date('Y-m-01');
foreach ($list as $rec) {
    if (strtotime($rec['date_creation']) >= strtotime($ceMoisDebut)) {
        $ceMois++;
    }
}

// Réclamations cette semaine
$cetteSemaine = 0;
$semaineDebut = date('Y-m-d', strtotime('monday this week'));
foreach ($list as $rec) {
    if (strtotime($rec['date_creation']) >= strtotime($semaineDebut)) {
        $cetteSemaine++;
    }
}

// Taux de traitement
$tauxTraitement = $totalReclamations > 0 ? round(($traitees / $totalReclamations) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Réclamations - ENGAGE Admin</title>
    
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background: var(--secondary-color);
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--primary-color);
        }
        
        .stats-section {
            margin-bottom: 30px;
        }
        
        .stats-section-title {
            color: var(--text-color);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-section-title i {
            color: var(--primary-color);
        }
        
        .reclamations-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .stat-card-large {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card-large::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
        }
        
        .stat-card-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stat-number-large {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text-color);
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-percentage {
            font-size: 1rem;
            color: var(--success);
            font-weight: 600;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .chart-title {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .chart-wrapper {
            height: 300px;
            position: relative;
        }
        
        .btn-back {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            background: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .reclamations-stats {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-wrapper {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-header">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Statistiques des Réclamations
            </h2>
            <a href="listReclamation.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <!-- Statistiques principales -->
        <div class="stats-section">
            <h3 class="stats-section-title">
                <i class="fas fa-chart-line"></i> Vue d'ensemble
            </h3>
            <div class="reclamations-stats">
                <div class="stat-card">
                    <span class="stat-number"><?= $totalReclamations ?></span>
                    <span class="stat-label">Total Réclamations</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $nonTraitees ?></span>
                    <span class="stat-label">Non Traitées</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $enCours ?></span>
                    <span class="stat-label">En Cours</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $traitees ?></span>
                    <span class="stat-label">Traitées</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $cetteSemaine ?></span>
                    <span class="stat-label">Cette Semaine</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $ceMois ?></span>
                    <span class="stat-label">Ce Mois</span>
                </div>
            </div>
        </div>

        <!-- Statistiques détaillées -->
        <div class="stats-section">
            <h3 class="stats-section-title">
                <i class="fas fa-chart-pie"></i> Statistiques détaillées
            </h3>
            <div class="reclamations-stats">
                <div class="stat-card-large">
                    <i class="fas fa-percentage stat-icon"></i>
                    <span class="stat-number-large"><?= $tauxTraitement ?>%</span>
                    <span class="stat-label">Taux de Traitement</span>
                </div>
                <div class="stat-card-large">
                    <i class="fas fa-comments stat-icon"></i>
                    <span class="stat-number-large"><?= $avecReponses ?></span>
                    <span class="stat-label">Avec Réponses</span>
                    <span class="stat-percentage"><?= $totalReclamations > 0 ? round(($avecReponses / $totalReclamations) * 100, 1) : 0 ?>%</span>
                </div>
                <div class="stat-card-large">
                    <i class="fas fa-comment-slash stat-icon"></i>
                    <span class="stat-number-large"><?= $sansReponses ?></span>
                    <span class="stat-label">Sans Réponses</span>
                    <span class="stat-percentage"><?= $totalReclamations > 0 ? round(($sansReponses / $totalReclamations) * 100, 1) : 0 ?>%</span>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <?php if ($totalReclamations > 0): ?>
        <div class="stats-section">
            <h3 class="stats-section-title">
                <i class="fas fa-chart-bar"></i> Graphiques et analyses
            </h3>
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-title">Répartition par Statut</div>
                    <div class="chart-wrapper">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-title">Répartition par Priorité</div>
                    <div class="chart-wrapper">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="stats-section">
            <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                <i class="fas fa-chart-bar" style="font-size: 4rem; opacity: 0.5; margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-color);">Aucune donnée disponible</h3>
                <p>Les statistiques apparaîtront ici une fois que des réclamations seront créées.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($totalReclamations > 0): ?>
    <script>
        // Données pour les graphiques
        const statusData = {
            labels: ['Non traitées', 'En cours', 'Traitées'],
            values: [<?= $statuts['Non traite'] ?>, <?= $statuts['En cours'] ?>, <?= $statuts['Traite'] ?>],
            colors: ['#ffc107', '#17a2b8', '#28a745']
        };

        const priorityData = {
            labels: ['Basse', 'Moyenne', 'Haute', 'Urgente'],
            values: [<?= $priorites['Basse'] ?>, <?= $priorites['Moyenne'] ?>, <?= $priorites['Haute'] ?>, <?= $priorites['Urgente'] ?>],
            colors: ['#6c757d', '#17a2b8', '#ffc107', '#dc3545']
        };

        // Graphique des statuts
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.values,
                        backgroundColor: statusData.colors,
                        borderWidth: 2,
                        borderColor: '#1f2235'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#ffffff',
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#ff4a57',
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // Graphique des priorités
        const priorityCtx = document.getElementById('priorityChart');
        if (priorityCtx) {
            new Chart(priorityCtx, {
                type: 'bar',
                data: {
                    labels: priorityData.labels,
                    datasets: [{
                        label: 'Nombre de réclamations',
                        data: priorityData.values,
                        backgroundColor: priorityData.colors,
                        borderColor: priorityData.colors,
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#ff4a57',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#b0b3c1',
                                stepSize: 1
                            },
                            grid: {
                                color: '#2d3047'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#b0b3c1'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>

