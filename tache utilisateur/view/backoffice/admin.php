<?php
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';

$utilisateurc = new utilisateurcontroller();


$count_utilisateurs = $utilisateurController->getUtilisateursCount();// AJOUT - méthode temporaire
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/all.css" />
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3> Backoffice</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-tachometer-alt"></i> Gestion d'article et quiz
                </a>
                <ul class="collapse list-unstyled" id="gestion-submenu">
                    <li>
                        <a href="index.php"><i class="fas fa-home"></i> Tableau de Bord</a>
                    </li>
                    <li>
                        <a href="addquiz.php"><i class="fas fa-plus-circle"></i> Ajouter Quiz</a>
                    </li>
                    <li>
                        <a href="#articles-section"><i class="fas fa-list"></i> Liste Articles</a>
                    </li>
                    <li>
                        <a href="#quiz-section"><i class="fas fa-list"></i> Liste Quiz</a>
                    </li>
                    <li>
                        <a href="#utilisateurs-section"><i class="fas fa-list"></i> Liste Utilisateurs</a> <!-- AJOUT -->
                    </li>
                    <li>
                        <a href="#stats-section"><i class="fas fa-list"></i> Statistiques</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <!-- Content -->
    <div id="content">
        <div class="container">
            <header class="header">
                <h1>Tableau de Bord Plateforme Quiz</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="location.href='addquiz.php'">Ajouter Quiz</button>
                </div>
            </header>

            <!-- NOUVELLE SECTION : Gestion des Utilisateurs -->
            <section class="content-section" id="utilisateurs-section">
                <div class="section-header">
                    <h2>Gestion des Utilisateurs</h2>
                    <p class="badge"><?php echo $count_utilisateurs; ?> utilisateurs</p>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <tr>
                            <th>ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Date de Naissance</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Type</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                        <?php 
                        $utilisateursData = $utilisateursResult->fetchAll();
                        foreach($utilisateursData as $utilisateur) { 
                        ?>
                        <tr>
                            <td class="id-cell">#<?php echo $utilisateur['id_util']; ?></td>
                            <td class="title-cell"><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                            <td class="title-cell"><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                            <td class="date-cell"><?php echo $utilisateur['dt_naiss']; ?></td>
                            <td class="content-cell"><?php echo htmlspecialchars($utilisateur['mail']); ?></td>
                            <td class="content-cell"><?php echo $utilisateur['num']; ?></td>
                            <td class="content-cell"><?php echo htmlspecialchars($utilisateur['typee']); ?></td>
                            <td class="actions-cell">
                                <a href="updateUtilisateur.php?id=<?php echo $utilisateur['id_util']; ?>" class="btn-action btn-edit">Modifier</a>
                                <a href="deleteUtilisateur.php?id=<?php echo $utilisateur['id_util']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </section>

            <section class="content-section" id="articles-section">
                <!-- Section articles existante... -->
                <div class="section-header">
                    <h2>Gestion des Articles</h2>
                    <p class="badge"><?php echo $count_articles; ?> articles</p>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Aperçu du Contenu</th>
                            <th>Date de Publication</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                        <?php foreach($articlesResult as $article) {?>
                        <tr>
                            <td class="id-cell">#<?php echo $article['id_article']; ?></td>
                            <td class="title-cell"><?php echo $article['titre']; ?></td>
                            <td class="content-cell"><?php echo substr($article['contenu'], 0, 100) . '...'; ?></td>
                            <td class="date-cell"><?php echo $article['date_publication']; ?></td>
                            <td class="actions-cell">
                                <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-edit">Modifier</a>
                            </td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </section>

            <section class="content-section" id="quiz-section">
                <!-- Section quiz existante... -->
                <div class="section-header">
                    <h2>Gestion des Quiz</h2>
                    <span class="badge"><?php echo $count_quiz; ?> quiz</span>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Réponses1</th>
                            <th>Réponses2</th>
                            <th>Réponses3</th>
                            <th>Bonne Réponse</th>
                            <th>ID Article</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                        <?php foreach($quizsResult as $quiz) {?>
                        <tr>
                            <td class="id-cell">#<?php echo $quiz['id_quiz']; ?></td>
                            <td class="question-cell"><?php echo $quiz['question']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse1']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse2']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse3']; ?></td>
                            <td class="correct-answer-cell">
                                <span class="correct-badge"><?php echo $quiz['bonne_reponse']; ?></span>
                            </td>
                            <td class="article-id-cell">#<?php echo $quiz['id_article']; ?></td>
                            <td class="actions-cell">
                                <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">Modifier</a>
                                <a href="deletequiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>         
            </section>

            <section class="stats-section" id="stats-section">
                <h2>Aperçu de la Plateforme</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $count_articles; ?></h3>
                            <p>Articles Totaux</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $count_quiz; ?></h3>
                            <p>Quiz Totaux</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $count_utilisateurs; ?></h3> <!-- MODIFICATION -->
                            <p>Utilisateurs Totaux</p> <!-- MODIFICATION -->
                        </div>
                    </div>
                    <div class="stat-card"> <!-- AJOUT -->
                        <div class="stat-info">
                            <h3><?php echo ($count_articles + $count_quiz + $count_utilisateurs); ?></h3>
                            <p>Contenu Total</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="j1.js"></script>
</body>
</html>