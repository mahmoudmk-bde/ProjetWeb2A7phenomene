<?php
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';

$utilisateurc = new utilisateurcontroller();

$utilisateursResult = $utilisateurc->listUtilisateurs();
$count_utilisateurs = $utilisateurc->getUtilisateursCount();
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
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3> Backoffice</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-tachometer-alt"></i> Gestion des utilisateurs
                </a>
                <ul class="collapse list-unstyled" id="gestion-submenu">
                    <li>
                        <a href="**********************"><i class="fas fa-list"></i></a>
                    </li>
                    <li>
                        <a href="#stats-section"><i class="fas fa-chart-bar"></i> </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="content">
        <div class="container">
            <header class="header">
                <h1>Tableau de Bord Administrateur</h1>
                <div class="header-actions">
                    
                </div>
            </header>

            <!-- Section : Gestion des Utilisateurs -->
            <section class="content-section" id="utilisateurs-section">
                <div class="section-header">
                    <h2>Gestion des Utilisateurs</h2>
                    
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
                        if($utilisateursResult) {
                            $utilisateursData = $utilisateursResult->fetchAll();
                            if(count($utilisateursData) > 0) {
                                foreach($utilisateursData as $utilisateur) { 
                        ?>
                        <tr>
                            <td class="id-cell">#<?php echo $utilisateur['id_util']; ?></td>
                            <td class="title-cell"><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                            <td class="title-cell"><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                            <td class="date-cell"><?php echo date('d/m/Y', strtotime($utilisateur['dt_naiss'])); ?></td>
                            <td class="content-cell"><?php echo htmlspecialchars($utilisateur['mail']); ?></td>
                            <td class="content-cell"><?php echo $utilisateur['num']; ?></td>
                            <td class="content-cell">
                                <span class="badge <?php echo $utilisateur['typee'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo htmlspecialchars($utilisateur['typee']); ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="updateUtilisateur.php?id=<?php echo $utilisateur['id_util']; ?>" class="btn-action btn-edit">Modifier</a>
                                <a href="deleteUtilisateur.php?id=<?php echo $utilisateur['id_util']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php 
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>Aucun utilisateur trouvé dans la base de données</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center; padding: 20px; color: red;'>Erreur lors du chargement des utilisateurs</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </section>

            <section class="stats-section" id="stats-section">
                <h2>Aperçu de la Plateforme</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $count_utilisateurs; ?></h3>
                            <p>Utilisateurs Totaux</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $count_utilisateurs; ?></h3>
                            <p>Membres Actifs</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="j1.js"></script>
</body>
</html>