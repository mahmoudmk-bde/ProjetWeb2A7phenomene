<?php
session_start();
include_once __DIR__ . '/../../../Controller/QuizController.php';
$quizController = new QuizController();
$historique = $quizController->getAllHistorique();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Liste Historique - Backoffice</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/all.css" />
    <link rel="stylesheet" href="../assets/css/indexx.css">
    <style>
    :root {
        --primary-color: #ff4a57;
        --secondary-color: #1f2235;
        --accent-color: #24263b;
        --text-color: #ffffff;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --border-color: #2d3047;
        --danger-color: #dc3545;
        --dark-color: #15172b;
        --light-color: #f8f9fa;
        --gray-color: #6c757d;
        --text-muted: #8a8da5;
        --transition: all 0.3s ease;
        --border-radius: 12px;
        --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    body {
        background: var(--secondary-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
        padding: 20px;
        background: var(--accent-color);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
    }
    
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .stat-box {
        background: var(--accent-color);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
    }
    
    .stat-box h3 {
        color: var(--primary-color);
        font-size: 2rem;
        margin: 0;
    }
    
    .stat-box p {
        color: var(--text-muted);
        margin: 5px 0 0 0;
    }
    
    .history-table-container {
        background: var(--accent-color);
        border-radius: var(--border-radius);
        padding: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    
    table {
        width: 100%;
        background: var(--accent-color);
        border-collapse: collapse;
    }
    
    thead {
        background: linear-gradient(45deg, var(--primary-color), #ff6b6b);
    }
    
    th {
        padding: 15px;
        text-align: left;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 0.9rem;
    }
    
    tbody tr {
        transition: var(--transition);
    }
    
    tbody tr:hover {
        background: rgba(255, 74, 87, 0.1);
    }
    
    .score-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .badge-high {
        background: rgba(40, 167, 69, 0.2);
        color: var(--success-color);
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    
    .badge-medium {
        background: rgba(255, 193, 7, 0.2);
        color: var(--warning-color);
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    .badge-low {
        background: rgba(220, 53, 69, 0.2);
        color: var(--danger-color);
        border: 1px solid rgba(220, 53, 69, 0.3);
    }
    
    .history-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn-view {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-view:hover {
        background: linear-gradient(45deg, #0056b3, #004099);
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }
    
    .btn-delete {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-delete:hover {
        background: linear-gradient(45deg, #c82333, #bd2130);
        transform: translateY(-2px);
        color: white;
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
    .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, var(--danger-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .actions-cell {
            display: flex;
            gap: 10px;
        }
        
        .view-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff6b7a 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .view-btn:hover {
            background: linear-gradient(135deg, #ff6b7a 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 74, 87, 0.3);
        }
        
</style>
</head>

<body>
    

    
        <div class="container">
            <div class="page-header">
                <h1 style="color: var(--text-color); margin: 0;">
                    <i class="fas fa-history"></i> Historique des Tentatives de Quiz
                </h1>
                <p style="color: var(--gray-color); margin: 10px 0 0 0;">
                    Consultez l'ensemble des tentatives de quiz effectuées par les utilisateurs
                </p>
            </div>

            <!-- Statistiques sommaires -->
            <?php
            $total_tentatives = count($historique);
            $average_score = 0;
            $high_scores = 0;
            
            if ($total_tentatives > 0) {
                $total_score = 0;
                foreach ($historique as $h) {
                    $total_score += $h['score'];
                    if ($h['score'] >= 80) {
                        $high_scores++;
                    }
                }
                $average_score = round($total_score / $total_tentatives, 1);
                $high_score_percentage = round(($high_scores / $total_tentatives) * 100, 1);
            }
            ?>
            
            <div class="stats-summary">
                <div class="stat-box">
                    <h3><?php echo $total_tentatives; ?></h3>
                    <p>Tentatives Total</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $average_score; ?> pts</h3>
                    <p>Score Moyen</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $high_score_percentage ?? 0; ?>%</h3>
                    <p>Haut Score (≥80 pts)</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo count(array_unique(array_column($historique, 'id_util'))); ?></h3>
                    <p>Utilisateurs Actifs</p>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Article/Quiz</th>
                                <th>Date Tentative</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historique)): ?>
                                <?php foreach($historique as $h): ?>
                                    <?php
                                    // Déterminer la classe CSS pour le score
                                    $score = $h['score'];
                                    if ($score >= 80) {
                                        $scoreClass = 'score-high';
                                        $badgeClass = 'badge-high';
                                    } elseif ($score >= 50) {
                                        $scoreClass = 'score-medium';
                                        $badgeClass = 'badge-medium';
                                    } else {
                                        $scoreClass = 'score-low';
                                        $badgeClass = 'badge-low';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $h['id_historique']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($h['username'] ?? 'Utilisateur'); ?></strong><br>
                                            <small style="color: var(--gray-color);">ID: <?php echo $h['id_util']; ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($h['article_titre'] ?? 'Quiz ' . $h['id_quiz']); ?></strong><br>
                                            <small style="color: var(--gray-color);">Quiz ID: <?php echo $h['id_quiz']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($h['date_tentative'])); ?><br>
                                            <small style="color: var(--gray-color);"><?php echo date('H:i:s', strtotime($h['date_tentative'])); ?></small>
                                        </td>
                                        <td class="score-cell <?php echo $scoreClass; ?>">
                                            <span class="score-badge <?php echo $badgeClass; ?>">
                                                <?php echo $h['score']; ?> pts
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                    
                                            <a href="deleteHistorique.php?id=<?php echo $h['id_historique']; ?>" 
                                               class="btn-danger"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet historique ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-history" style="font-size: 3rem; color: var(--border-color); margin-bottom: 15px; display: block;"></i>
                                        <p style="color: var(--gray-color);">Aucun historique disponible</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
    
    <!-- Inclure les scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/index.js"></script>
</body>
</html>