<?php 
// CHEMINS AVEC "condidature"
require_once (__DIR__."/../../model/condidature.php");
require_once (__DIR__."/../../model/mission.php");
require_once (__DIR__."/../../controller/condidaturecontroller.php");
require_once (__DIR__."/../../controller/missioncontroller.php");

$condidaturecontroller = new condidaturecontroller();
$missioncontroller = new missioncontroller();

// Récupérer l'ID de la mission depuis l'URL
$mission_id = $_GET["mission_id"] ?? 0;
$mission = $missioncontroller->getmissionbyid($mission_id);

if(isset($_POST["pseudo_gaming"]) 
    && isset($_POST["message_motivation"]) 
    && isset($_POST["niveau_experience"]) 
    && isset($_POST["heures_jeu_semaine"])
) {
    if(!empty($_POST["pseudo_gaming"]) 
        && !empty($_POST["message_motivation"]) 
        && !empty($_POST["niveau_experience"]) 
        && !empty($_POST["heures_jeu_semaine"])
    ) {
        // Gérer les disponibilités
        $disponibilites = $_POST["disponibilites"] ?? [];
        if (is_array($disponibilites)) {
            $disponibilites = implode(', ', $disponibilites);
        }
        
        // Ajouter une condidature
        $condidature = new condidature(
            null, // id
            $mission_id,
            1, // volontaire_id (à adapter)
            $_POST["pseudo_gaming"],
            $_POST["message_motivation"],
            $_POST["niveau_experience"],
            $_POST["heures_jeu_semaine"],
            $disponibilites
        );

        $condidaturecontroller->addcondidature($condidature);
        header("Location: condidaturelist.php?success=condidature_added");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ENGAGE - Postuler à une Mission</title>
    <link rel="icon" href="../img/favicon.png" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/owl.carousel.min.css" />
    <link rel="stylesheet" href="../css/all.css" />
    <link rel="stylesheet" href="../css/flaticon.css" />
    <link rel="stylesheet" href="../css/themify-icons.css" />
    <link rel="stylesheet" href="../css/magnific-popup.css" />
    <link rel="stylesheet" href="../css/slick.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/mission.css" />
</head>
<body>
    <div class="body_bg">
        <!-- Header -->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="../mission/missionlist.php">
                                <img src="../img/logo.png" alt="ENGAGE Logo" />
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Section Breadcrumb -->
        <section class="breadcrumb breadcrumb_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb_iner text-center">
                            <div class="breadcrumb_iner_item">
                                <h2>Postuler à une Mission</h2>
                                <p>Rejoignez une mission et faites la différence</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section_padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <?php if($mission): ?>
                            <!-- Carte de la mission -->
                            <div class="card mb-4 mission-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h3><?php echo htmlspecialchars($mission["titre"]); ?></h3>
                                        <span class="badge bg-<?= $mission['niveau_difficulte'] == 'facile' ? 'success' : 'warning' ?>">
                                            <?php echo ucfirst($mission["niveau_difficulte"]); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>🎮 Jeu:</strong> <?php echo htmlspecialchars($mission["jeu"]); ?></p>
                                            <p><strong>📝 Type:</strong> <?php echo htmlspecialchars($mission["type_mission"]); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>⏱️ Engagement:</strong> <?php echo $mission["heures_semaine"]; ?>h/semaine</p>
                                            <p><strong>📅 Durée:</strong> <?php echo $mission["duree_totale"]; ?> semaines</p>
                                        </div>
                                    </div>
                                    
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($mission["description"]); ?></p>
                                    
                                    <div class="mission-info">
                                        <strong>Compétences requises:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($mission["competences_requises"])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulaire de condidature -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-4">📨 Formulaire de Candidature</h4>
                                    
                                    <form method="POST" action="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pseudo_gaming">Pseudo Gaming *</label>
                                                    <input type="text" class="form-control" id="pseudo_gaming" name="pseudo_gaming" 
                                                           placeholder="Votre pseudo dans le jeu" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="niveau_experience">Niveau d'expérience *</label>
                                                    <select class="form-control" id="niveau_experience" name="niveau_experience" required>
                                                        <option value="">Sélectionnez votre niveau</option>
                                                        <option value="debutant">Débutant</option>
                                                        <option value="intermediaire">Intermédiaire</option>
                                                        <option value="expert">Expert</option>
                                                        <option value="professionnel">Professionnel</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="heures_jeu_semaine">Heures de jeu par semaine *</label>
                                            <input type="number" class="form-control" id="heures_jeu_semaine" name="heures_jeu_semaine" 
                                                   min="1" max="40" placeholder="Ex: 10" required>
                                            <small class="form-text text-muted">Nombre d'heures que vous pouvez consacrer au jeu par semaine</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="message_motivation">Message de motivation *</label>
                                            <textarea class="form-control" id="message_motivation" name="message_motivation" 
                                                      rows="6" placeholder="Pourquoi voulez-vous participer à cette mission ? Qu'est-ce qui vous motive ? Quelles sont vos expériences ?" required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Disponibilités</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="disponibilites[]" value="weekend" id="weekend">
                                                        <label class="form-check-label" for="weekend">Week-end</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="disponibilites[]" value="soiree" id="soiree">
                                                        <label class="form-check-label" for="soiree">Soirées</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="disponibilites[]" value="journee" id="journee">
                                                        <label class="form-check-label" for="journee">Journée</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fa fa-paper-plane mr-2"></i>Envoyer ma Candidature
                                            </button>
                                            <a href="../mission/missionlist.php" class="btn btn-secondary btn-lg">Retour aux Missions</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                <h4>Mission non trouvée</h4>
                                <p>La mission que vous essayez de rejoindre n'existe pas ou a été supprimée.</p>
                                <a href="../mission/missionlist.php" class="btn btn-primary">Retour aux Missions</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>