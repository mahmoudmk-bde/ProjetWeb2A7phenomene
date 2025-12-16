<?php
session_start();
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();

// Récupérer l'ID de l'article depuis l'URL
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations de l'article
$article_info = $quizController->getArticleById($article_id);

// Récupérer les utilisateurs qui ont passé le quiz de cet article
$usersResult = $quizController->getUsersByArticle($article_id);
$count_users = count($usersResult);

// DEBUG: Afficher ce que retourne la requête
error_log("DEBUG histr_user.php - Article ID: " . $article_id);
error_log("DEBUG histr_user.php - Nombre d'utilisateurs: " . $count_users);

// Calculer les statistiques
$total_score = 0;
$high_scores = 0;
$average_score = 0;

foreach ($usersResult as $user) {
    if (isset($user['score']) && is_numeric($user['score'])) {
        $total_score += (int)$user['score'];
        if ($user['score'] >= 80) {
            $high_scores++;
        }
    }
}

if ($count_users > 0 && $total_score > 0) {
    $average_score = round($total_score / $count_users, 1);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Utilisateurs - Article #<?php echo $article_id; ?> | Backoffice</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/indexx.css">
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
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .table-container {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: var(--border-radius);
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
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
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff6b7a 100%);
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
        
        .score-cell {
            font-weight: 600;
        }
        
        .score-high { color: var(--success-color); }
        .score-medium { color: var(--warning-color); }
        .score-low { color: var(--danger-color); }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .page-header {
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .stat-box h3 {
            color: var(--primary-color);
            font-size: 2rem;
            margin: 0;
        }
        
        .stat-box p {
            color: var(--gray-color);
            margin: 5px 0 0 0;
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
        
        .btn-back {
            background: linear-gradient(135deg, var(--gray-color) 0%, #868e96 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            background: linear-gradient(135deg, #868e96 0%, var(--gray-color) 100%);
            transform: translateY(-2px);
        }
        
        .score-badge {
            display: inline-block;
            padding: 4px 12px;
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
        
        .article-info {
            margin-bottom: 20px;
            padding: 15px;
            background: var(--accent-color);
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }
        
        .article-info h4 {
            color: var(--text-color);
            margin: 0 0 10px 0;
        }
        
        .article-info p {
            color: var(--gray-color);
            margin: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--gray-color);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3>Backoffice</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="true" class="dropdown-toggle">
                    <i class="fas fa-tachometer-alt"></i> Gestion d'article et quiz
                </a>
                <ul class="collapse list-unstyled show" id="gestion-submenu">
                    <li>
                        <a href="index.php">
                            <i class="fas fa-home"></i> Tableau de Bord
                        </a>
                    </li>
                    <li>
                        <a href="addarticle.php">
                            <i class="fas fa-plus"></i> Ajouter un article
                        </a>
                    </li>
                    <li>
                        <a href="addquiz.php">
                            <i class="fas fa-plus"></i> Ajouter un quiz
                        </a>
                    </li>
                    <li>
                        <a href="listeArticle.php">
                            <i class="fas fa-list"></i> Liste des articles
                        </a>
                    </li>
                    <li>
                        <a href="listeQuiz.php">
                            <i class="fas fa-list"></i> Liste des quiz
                        </a>
                    </li>
                    <li>
                        <a href="listeHistorique.php">
                            <i class="fas fa-history"></i> Historique
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="content">
        <div class="container">
            <!-- En-tête avec bouton retour -->
            <div class="page-header">
                <div>
                    <h1 style="color: var(--text-color); margin: 0;">
                        <i class="fas fa-users"></i> Utilisateurs du Quiz
                    </h1>
                    <p style="color: var(--gray-color); margin: 10px 0 0 0;">
                        Liste des utilisateurs ayant passé le quiz de cet article
                    </p>
                </div>
                <a href="listeArticle.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour aux articles
                </a>
            </div>

            <!-- Informations sur l'article -->
            <?php if ($article_info): ?>
            <div class="article-info">
                <h4>
                    <i class="fas fa-file-alt"></i> 
                    <?php echo htmlspecialchars($article_info['titre']); ?>
                </h4>
                <p>
                    <strong>ID:</strong> #<?php echo $article_info['id_article']; ?> | 
                    <strong>Date:</strong> <?php echo date('d/m/Y', strtotime($article_info['date_publication'])); ?> | 
                    <strong>Utilisateurs:</strong> <?php echo $count_users; ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Statistiques sommaires -->
            <div class="stats-summary">
                <div class="stat-box">
                    <h3><?php echo $count_users; ?></h3>
                    <p>Utilisateurs Total</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $average_score; ?> pts</h3>
                    <p>Score Moyen</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $high_scores; ?></h3>
                    <p>Haut Scores (≥80 pts)</p>
                </div>
                <div class="stat-box">
                    <h3>
                        <?php 
                        echo $count_users > 0 ? round(($high_scores / $count_users) * 100, 1) : 0;
                        ?>%
                    </h3>
                    <p>Taux de Réussite</p>
                </div>
            </div>

            <!-- Tableau des utilisateurs avec SEULEMENT 3 colonnes -->
            <div class="table-container">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Date Tentative</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($count_users > 0): ?>
                                <?php foreach($usersResult as $user): ?>
                                    <?php
                                    // Déterminer la classe CSS pour le score
                                    $score = isset($user['score']) ? (int)$user['score'] : 0;
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
                                    
                                    // Formater le nom complet
                                    $fullname = '';
                                    if (isset($user['fullname'])) {
                                        $fullname = $user['fullname'];
                                    } elseif (isset($user['prenom']) && isset($user['nom'])) {
                                        $fullname = trim($user['prenom'] . ' ' . $user['nom']);
                                    } elseif (isset($user['prenom'])) {
                                        $fullname = $user['prenom'];
                                    } else {
                                        $fullname = 'Utilisateur';
                                    }
                                    
                                    // ID utilisateur (pour référence)
                                    $user_id = $user['id_util'] ?? 0;
                                    
                                    // Date
                                    $date_tentative = $user['date_tentative'] ?? null;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($fullname); ?></strong><br>
                                            <small style="color: var(--gray-color);">
                                                ID: <?php echo $user_id; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($date_tentative) {
                                                echo date('d/m/Y', strtotime($date_tentative)); 
                                                echo '<br><small style="color: var(--gray-color);">';
                                                echo date('H:i:s', strtotime($date_tentative));
                                                echo '</small>';
                                            } else {
                                                echo '<span style="color: var(--gray-color);">Non disponible</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="score-cell <?php echo $scoreClass; ?>">
                                            <span class="score-badge <?php echo $badgeClass; ?>">
                                                <?php echo $score; ?> pts
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 40px;">
                                        <div class="empty-state">
                                            <i class="fas fa-users-slash" style="font-size: 3rem; color: var(--border-color); margin-bottom: 15px; display: block;"></i>
                                            <h3 style="color: var(--text-color); margin-bottom: 10px;">Aucun utilisateur trouvé</h3>
                                            <p style="color: var(--gray-color); margin-bottom: 20px;">
                                                Aucun utilisateur n'a encore passé le quiz de cet article.
                                            </p>
                                            <a href="listeArticle.php" class="btn-back">
                                                <i class="fas fa-arrow-left"></i> Retour aux articles
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inclure les scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>