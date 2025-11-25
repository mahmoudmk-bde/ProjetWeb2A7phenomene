<?php
require_once "../../Controller/ReclamationController.php";

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $rec = new Reclamation($_POST['sujet'], $_POST['description'], $_POST['email']);
        $ctrl = new ReclamationController();
        $ctrl->addReclamation($rec);
        $success_message = "Réclamation ajoutée avec succès !";
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ajouter une Réclamation</title>
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form_section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        .form_container {
            background: white;
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .form_title {
            text-align: center;
            margin-bottom: 40px;
        }
        .form_title h2 {
            font-size: 32px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .form_title p {
            color: #7f8c8d;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
        }
        .form-group input,
        .form-group textarea {
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff4a57;
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.1);
            outline: none;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn_submit {
            background: linear-gradient(45deg, #ff4a57, #ff6b7a);
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn_submit:hover {
            background: linear-gradient(45deg, #ff6b7a, #ff4a57);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 30px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .back_link {
            text-align: center;
            margin-top: 20px;
        }
        .back_link a {
            color: #ff4a57;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .back_link a:hover {
            color: #ff6b7a;
        }
        .hidden { display: none; }
        .history-item { border-bottom: 1px solid #eee; padding: 12px 0; }
        .history-item:last-child { border-bottom: none; }
        .history-meta { color: #7f8c8d; font-size: 13px; margin-bottom: 6px; }
        .lookup-row { display:flex; gap:8px; margin-bottom:18px; }
        .lookup-row .form-control { flex:1; }
    </style>
</head>

<body>
    <div class="body_bg">
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index.php">
                                <img src="img/logo.png" alt="logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="fighter.html">Fighter</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">Team</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.html">Contact</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="addreclamation.php">Réclamer</a>
                                    </li>
                                </ul>
                            </div>
                            
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <section class="form_section">
            <div class="form_container">
                <div class="form_title">
                    <h2>Déposer une Réclamation</h2>
                    <p>Veuillez remplir le formulaire ci-dessous pour soumettre votre réclamation</p>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Email lookup: require email first, show history, then enable add form -->
                <div id="emailLookupSection">
                    <div class="lookup-row">
                        <input type="email" id="lookupEmail" class="form-control" placeholder="Entrez votre email pour voir l'historique">
                        <button id="lookupBtn" class="btn_submit" style="width:auto;padding:10px 18px;">Voir l'historique</button>
                    </div>
                    <div id="lookupMessage" style="margin-bottom:12px;color:#e74c3c"></div>
                    <div id="historyContainer"></div>
                </div>


                <form id="reclamationForm" method="POST" action="" novalidate class="hidden">
                    <div class="form-group">
                        <label for="sujet">Sujet de la réclamation</label>
                        <input type="text" class="form-control" id="sujet" name="sujet" 
                               placeholder="Entrez le sujet" aria-describedby="sujetHelp">
                        <div class="invalid-feedback" id="sujetHelp"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Votre email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Entrez votre email" aria-describedby="emailHelp">
                        <div class="invalid-feedback" id="emailHelp"></div>
                        <div style="margin-top:8px;">
                            <button type="button" id="changeEmailBtn" class="btn btn-link" style="padding:0;">Modifier l'email</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description de la réclamation</label>
                        <textarea class="form-control" id="description" name="description" 
                                  placeholder="Décrivez en détail votre réclamation..." aria-describedby="descriptionHelp"></textarea>
                        <div class="invalid-feedback" id="descriptionHelp"></div>
                    </div>

                    <button type="submit" class="btn_submit">
                        <i class="fas fa-paper-plane"></i> Soumettre ma réclamation
                    </button>
                </form>

                <div class="back_link">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </section>
    </div>
    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/all.js"></script>
    <script src="js/form-validation.js"></script>
</body>

</html>
