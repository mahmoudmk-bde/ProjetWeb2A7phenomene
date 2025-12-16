<?php
session_start();

// Run database migrations
require_once __DIR__ . '/../../db_migrations.php';

require_once "../../controller/ReclamationController.php";
require_once "../../controller/ReclamationClassifier.php";
require_once "../../controller/missioncontroller.php";

$success_message = '';
$error_message = '';
$sessionEmail = $_SESSION['user_email'] ?? '';

// Cette page nécessite un utilisateur connecté pour lier la réclamation
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=' . urlencode('addreclamation.php'));
    exit();
}

// Chargement des listes (missions, événements, partenaires, utilisateurs)
$missions = [];
$evenements = [];
$partenaires = [];
$utilisateurs = [];

try {
    $missionCtrl = new missioncontroller();
    $missions = $missionCtrl->missionliste();

    $pdo = config::getConnexion();
    $evenements = $pdo->query("SELECT id_evenement, titre, date_evenement FROM evenement ORDER BY date_evenement DESC")
        ->fetchAll(PDO::FETCH_ASSOC);
    $partenaires = $pdo->query("SELECT id, nom FROM partenaires ORDER BY nom ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
    $utilisateurs = $pdo->query("SELECT id_util, prenom, nom, mail FROM utilisateur ORDER BY prenom, nom")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Impossible de charger toutes les données de sélection : " . $e->getMessage();
}

function findLabelById(array $items, string $idKey, string $labelKey, $id): ?string {
    foreach ($items as $item) {
        if ((string)($item[$idKey] ?? '') === (string)$id) {
            return $item[$labelKey] ?? null;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $email = $_POST['email'] ?? $sessionEmail;
        $sujet = $_POST['sujet'] ?? '';
        $description = $_POST['description'] ?? '';

        $type = $_POST['type_reclamation'] ?? 'autre';
        $missionId = $_POST['mission_id'] ?? null;
        $evenementId = $_POST['evenement_id'] ?? null;
        $partenaireId = $_POST['partenaire_id'] ?? null;
        $utilisateurCibleId = $_POST['utilisateur_cible_id'] ?? null;
        $techniqueDetail = $_POST['technique_detail'] ?? '';

        $contextPieces = ["Type: " . ucfirst($type)];
        if ($type === 'mission' && $missionId) {
            $missionLabel = findLabelById($missions, 'id', 'titre', $missionId);
            $contextPieces[] = "Mission #" . intval($missionId) . ($missionLabel ? " (" . $missionLabel . ")" : '');
        }
        if ($type === 'evenement' && $evenementId) {
            $eventLabel = findLabelById($evenements, 'id_evenement', 'titre', $evenementId);
            $contextPieces[] = "Événement #" . intval($evenementId) . ($eventLabel ? " (" . $eventLabel . ")" : '');
        }
        if ($type === 'partenaire' && $partenaireId) {
            $partLabel = findLabelById($partenaires, 'id', 'nom', $partenaireId);
            $contextPieces[] = "Partenaire #" . intval($partenaireId) . ($partLabel ? " (" . $partLabel . ")" : '');
        }
        if ($type === 'utilisateur' && $utilisateurCibleId) {
            $userLabel = findLabelById($utilisateurs, 'id_util', 'prenom', $utilisateurCibleId);
            $contextPieces[] = "Utilisateur #" . intval($utilisateurCibleId) . ($userLabel ? " (" . $userLabel . ")" : '');
        }
        if ($type === 'technique' && $techniqueDetail) {
            $contextPieces[] = "Détail technique: " . trim($techniqueDetail);
        }

        $contextString = implode(' | ', $contextPieces);
        $descriptionWithContext = $contextString . "\n\n" . trim($description);
        $sujetDecorated = '[' . ucfirst($type) . '] ' . $sujet;

        // 🤖 AI CLASSIFICATION - Smart Auto-Detection
        $classification = ReclamationClassifier::classify($sujetDecorated, $descriptionWithContext);
        
        // Allow manual override if provided
        $finalPriority = !empty($_POST['priority_override']) ? $_POST['priority_override'] : $classification['priority_label'];
        $finalCategory = $classification['category'];
        $finalDepartment = $classification['department'];

        $utilisateur_id = $_SESSION['user_id'];
        $product_id = null;

        $rec = new Reclamation(
            $sujetDecorated,
            $descriptionWithContext,
            $email,
            "Non traite",
            $utilisateur_id,
            $product_id,
            $finalPriority,
            $finalCategory,
            $finalDepartment
        );

        $ctrl = new ReclamationController();
        $ctrl->addReclamation($rec);
        $_SESSION['reclamation_success'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENGAGE – Réclamation</title>

    <!-- Favicon -->
    <link rel="icon" href="../img/logo.png">

    <!-- CSS identique aux autres pages frontoffice -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .user-dropdown a:hover {
            background: #f8f9fa;
            color: var(--primary);
            transform: translateX(5px);
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
            color: #dc3545;
        }
        
        .user-dropdown a:last-child:hover {
            background: #dc3545;
            color: white;
        }
        
        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .user-wrapper:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }
        
        .user-avatar i {
            color: white;
            font-size: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <style>
        .form_section {
            padding: 80px 0;
        }
        .form_container {
            background: rgba(15, 16, 32, 0.95);
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.6);
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
            color: #fff;
            margin-bottom: 10px;
        }
        .form_title p {
            color: rgba(255,255,255,0.7);
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
        }
        .form-group input,
        .form-group textarea {
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff4a57;
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.1);
            outline: none;
            background: rgba(255,255,255,0.12);
        }
        /* All select styling removed - using inline styles instead */
        option {
            background-color: #2d3142 !important;
            color: white !important;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255,255,255,0.7);
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
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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
    </style>
    <style>
        /* NUCLEAR OVERRIDE - After all CSS files */
        select, select.form-control, .form-group select {
            background-color: #2d3142 !important;
            color: white !important;
            border: 2px solid #ff4a57 !important;
            appearance: auto !important;
            background-image: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        
        option {
            background-color: #2d3142 !important;
            color: white !important;
        }
        
        option:checked {
            background-color: #ff4a57 !important;
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="body_bg">
        <?php include 'header_common.php'; ?>
        <section class="form_section section_padding">
            <div class="form_container">
                <div class="form_title">
                    <h2>Déposer une Réclamation</h2>
                    <p>Veuillez remplir le formulaire ci-dessous pour soumettre votre réclamation</p>
                </div>

                <?php if (isset($_SESSION['reclamation_success']) && $_SESSION['reclamation_success']): ?>
                    <div id="successAlert" class="alert" style="background-color: #d4edda; border: 2px solid #28a745; border-radius: 8px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.5rem;"></i>
                        <div style="color: #155724; font-weight: 600;">Réclamation envoyée avec succès!</div>
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('successAlert').style.display = 'none';
                        }, 5000);
                    </script>
                    <?php unset($_SESSION['reclamation_success']); ?>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire direct de réclamation -->
                <form id="reclamationForm" method="POST" action="" novalidate>
                    <!-- Description first so AI can auto-suggest the rest -->
                    <div class="form-group">
                        <label for="description">Description de la réclamation</label>
                        <textarea class="form-control" id="description" name="description" 
                                  placeholder="Décrivez en détail votre réclamation..." aria-describedby="descriptionHelp"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback" id="descriptionHelp"></div>
                    </div>

                    <div class="form-group">
                        <label for="type_reclamation">Type de réclamation</label>
                        <select id="type_reclamation" name="type_reclamation" required style="width: 100%; border: 2px solid #ff4a57; border-radius: 8px; padding: 12px 15px; font-size: 16px; font-family: Arial, sans-serif; font-weight: normal; color: #ffffff; background-color: #2d3142; cursor: pointer; line-height: normal;">
                            <option value="" style="background-color: #2d3142; color: #ffffff; font-size: 16px;">Choisissez le type</option>
                            <option value="mission" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'mission') ? 'selected' : '' ?>>Mission</option>
                            <option value="evenement" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'evenement') ? 'selected' : '' ?>>Événement</option>
                            <option value="partenaire" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'partenaire') ? 'selected' : '' ?>>Partenaire</option>
                            <option value="utilisateur" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'utilisateur') ? 'selected' : '' ?>>Utilisateur</option>
                            <option value="technique" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'technique') ? 'selected' : '' ?>>Technique</option>
                            <option value="autre" style="background-color: #2d3142; color: #ffffff; font-size: 16px;" <?= (($_POST['type_reclamation'] ?? '') === 'autre') ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>

                    <div class="form-group conditional-field d-none" data-block="mission">
                        <label for="mission_id">Mission concernée</label>
                        <select id="mission_id" name="mission_id" style="width: 100%; border: 2px solid #ff4a57; border-radius: 8px; padding: 12px 15px; font-size: 16px; font-family: Arial, sans-serif; font-weight: normal; color: #ffffff; background-color: #2d3142; cursor: pointer; line-height: normal;">
                            <option value="">Sélectionnez une mission</option>
                            <?php foreach ($missions as $mission): ?>
                                <option value="<?= htmlspecialchars($mission['id']) ?>" <?= (($_POST['mission_id'] ?? '') == $mission['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($mission['titre'] ?? ('Mission #' . $mission['id'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group conditional-field d-none" data-block="evenement">
                        <label for="evenement_id">Événement concerné</label>
                        <select id="evenement_id" name="evenement_id" style="width: 100%; border: 2px solid #ff4a57; border-radius: 8px; padding: 12px 15px; font-size: 16px; font-family: Arial, sans-serif; font-weight: normal; color: #ffffff; background-color: #2d3142; cursor: pointer; line-height: normal;">
                            <option value="">Sélectionnez un événement</option>
                            <?php foreach ($evenements as $event): ?>
                                <option value="<?= htmlspecialchars($event['id_evenement']) ?>" <?= (($_POST['evenement_id'] ?? '') == $event['id_evenement']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(($event['titre'] ?? 'Événement') . ' – ' . ($event['date_evenement'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group conditional-field d-none" data-block="partenaire">
                        <label for="partenaire_id">Partenaire concerné</label>
                        <select id="partenaire_id" name="partenaire_id" style="width: 100%; border: 2px solid #ff4a57; border-radius: 8px; padding: 12px 15px; font-size: 16px; font-family: Arial, sans-serif; font-weight: normal; color: #ffffff; background-color: #2d3142; cursor: pointer; line-height: normal;">
                            <option value="">Sélectionnez un partenaire</option>
                            <?php foreach ($partenaires as $p): ?>
                                <option value="<?= htmlspecialchars($p['id']) ?>" <?= (($_POST['partenaire_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nom'] ?? ('Partenaire #' . $p['id'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group conditional-field d-none" data-block="utilisateur">
                        <label for="utilisateur_cible_id">Utilisateur concerné</label>
                        <select id="utilisateur_cible_id" name="utilisateur_cible_id" style="width: 100%; border: 2px solid #ff4a57; border-radius: 8px; padding: 12px 15px; font-size: 16px; font-family: Arial, sans-serif; font-weight: normal; color: #ffffff; background-color: #2d3142; cursor: pointer; line-height: normal;">
                            <option value="">Sélectionnez un utilisateur</option>
                            <?php foreach ($utilisateurs as $u): ?>
                                <option value="<?= htmlspecialchars($u['id_util']) ?>" <?= (($_POST['utilisateur_cible_id'] ?? '') == $u['id_util']) ? 'selected' : '' ?>>
                                    ID #<?= htmlspecialchars($u['id_util']) ?> – <?= htmlspecialchars(trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group conditional-field d-none" data-block="technique">
                        <label for="technique_detail">Détail technique</label>
                        <input type="text" class="form-control" id="technique_detail" name="technique_detail" 
                               placeholder="Précisez le problème technique" value="<?= htmlspecialchars($_POST['technique_detail'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="sujet">Sujet de la réclamation</label>
                        <input type="text" class="form-control" id="sujet" name="sujet" 
                               placeholder="Entrez le sujet" aria-describedby="sujetHelp" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>">
                        <div class="invalid-feedback" id="sujetHelp"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Votre email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Entrez votre email" aria-describedby="emailHelp" value="<?= htmlspecialchars($_POST['email'] ?? $sessionEmail) ?>">
                        <div class="invalid-feedback" id="emailHelp"></div>
                    </div>

                    <!-- 🤖 AI Classification Preview -->
                    <div id="ai-classification-preview" style="display: none; background: linear-gradient(135deg, rgba(255, 74, 87, 0.08) 0%, rgba(45, 49, 66, 0.18) 100%); border: 2px solid rgba(255, 74, 87, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <i class="fas fa-robot" style="font-size: 24px; color: var(--primary);"></i>
                            <h5 style="margin: 0; color: #fff; font-weight: 600;">Classification Automatique</h5>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                            <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 12px; margin-bottom: 5px;">Catégorie</div>
                                <div id="ai-category" style="color: var(--primary); font-weight: 600; font-size: 15px;">-</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 12px; margin-bottom: 5px;">Priorité</div>
                                <div id="ai-priority" style="color: var(--primary-light); font-weight: 600; font-size: 15px;">-</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px;">
                                <div style="color: rgba(255,255,255,0.7); font-size: 12px; margin-bottom: 5px;">Département</div>
                                <div id="ai-department" style="color: var(--primary); font-weight: 600; font-size: 15px;">-</div>
                            </div>
                        </div>
                        <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                                <div id="ai-confidence-bar" style="height: 100%; background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%); width: 0%; transition: width 0.5s ease;"></div>
                            </div>
                            <small id="ai-confidence-text" style="color: rgba(255,255,255,0.7); font-size: 12px;">Confiance: 0%</small>
                        </div>
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

        <!-- FOOTER identique -->
        <footer class="footer-area">
            <div class="container text-center text-white">
                <p style="margin:0; padding:20px 0;">
                    © 2025 ENGAGE Platform – Développé par les phenomenes
                </p>
            </div>
        </footer>
    </div>

    <!-- JS identiques aux autres pages -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
                // 🤖 Live AI Classification
                (function() {
                    const sujetInput = document.getElementById('sujet');
                    const descriptionInput = document.getElementById('description');
                    const previewBox = document.getElementById('ai-classification-preview');
                    const categoryEl = document.getElementById('ai-category');
                    const priorityEl = document.getElementById('ai-priority');
                    const departmentEl = document.getElementById('ai-department');
                    const confidenceBar = document.getElementById('ai-confidence-bar');
                    const confidenceText = document.getElementById('ai-confidence-text');
                    const typeSelect = document.getElementById('type_reclamation');
                    const missionSelect = document.getElementById('mission_id');
                    const eventSelect = document.getElementById('evenement_id');
                    const partnerSelect = document.getElementById('partenaire_id');
                    const userSelect = document.getElementById('utilisateur_cible_id');
                    const techniqueInput = document.getElementById('technique_detail');

                    // Track if user manually changed selections to avoid overwriting
                    const locks = {
                        type: false,
                        mission: false,
                        event: false,
                        partner: false,
                        user: false
                    };
                    
                    // Only lock when user manually changes (not programmatic change)
                    if (typeSelect) typeSelect.addEventListener('change', (e) => { 
                        if (e.isTrusted) locks.type = true; 
                    });
                    if (missionSelect) missionSelect.addEventListener('change', (e) => { 
                        if (e.isTrusted && missionSelect.value) locks.mission = true; 
                    });
                    if (eventSelect) eventSelect.addEventListener('change', (e) => { 
                        if (e.isTrusted && eventSelect.value) locks.event = true; 
                    });
                    if (partnerSelect) partnerSelect.addEventListener('change', (e) => { 
                        if (e.isTrusted && partnerSelect.value) locks.partner = true; 
                    });
                    if (userSelect) userSelect.addEventListener('change', (e) => { 
                        if (e.isTrusted && userSelect.value) locks.user = true; 
                    });
                    if (techniqueInput) techniqueInput.addEventListener('input', () => { locks.technique = true; });

                    function normalize(str) {
                        return (str || '').toString().toLowerCase();
                    }

                    function getTokens(text) {
                        return normalize(text).split(/[^a-z0-9éèêàùûôç]+/i).filter(w => w.length >= 3);
                    }

                    function autoSelectByMatch(selectEl, lockKey, text) {
                        if (!selectEl) return;
                        // Only skip if user manually selected something AND it's still set
                        if (locks[lockKey] && selectEl.value) return;
                        
                        const tokens = getTokens(text);
                        if (!tokens.length) {
                            autoSelectFirst(selectEl, lockKey);
                            return;
                        }
                        const options = Array.from(selectEl.options).slice(1); // skip placeholder
                        let best = null;
                        let bestScore = 0;
                        options.forEach(opt => {
                            const label = normalize(opt.text);
                            let score = 0;
                            tokens.forEach(t => { if (label.includes(t)) score += t.length; });
                            if (score > bestScore) { bestScore = score; best = opt; }
                        });
                        if (best) {
                            selectEl.value = best.value;
                            selectEl.dispatchEvent(new Event('change'));
                        } else {
                            autoSelectFirst(selectEl, lockKey);
                        }
                    }

                    function bestEntityMatch(text) {
                        const buckets = [];
                        const tokens = getTokens(text);
                        if (!tokens.length) return null;

                        function scoreSelect(selectEl, typeKey) {
                            if (!selectEl) return;
                            const options = Array.from(selectEl.options).slice(1);
                            let best = null, bestScore = 0;
                            options.forEach(opt => {
                                const label = normalize(opt.text);
                                let score = 0;
                                tokens.forEach(t => { if (label.includes(t)) score += t.length; });
                                if (score > bestScore) { bestScore = score; best = opt; }
                            });
                            if (bestScore > 0 && best) {
                                buckets.push({ type: typeKey, option: best, score: bestScore });
                            }
                        }

                        scoreSelect(missionSelect, 'mission');
                        scoreSelect(eventSelect, 'evenement');
                        scoreSelect(partnerSelect, 'partenaire');
                        scoreSelect(userSelect, 'utilisateur');

                        if (!buckets.length) return null;
                        buckets.sort((a, b) => b.score - a.score);
                        return buckets[0];
                    }

                    function fillTechnique(text) {
                        if (!techniqueInput || locks.technique) return;
                        const snippet = text.trim();
                        if (!snippet) return;
                        techniqueInput.value = snippet.slice(0, 180);
                        locks.technique = true;
                    }

                    function mapCategoryToType(category) {
                        switch (category) {
                            case 'mission': return 'mission';
                            case 'event': return 'evenement';
                            case 'technical': return 'technique';
                            case 'payment': return 'technique';
                            case 'delivery': return 'technique';
                            case 'partner': return 'partenaire';
                            case 'hr': return 'utilisateur';
                            case 'store': return 'partenaire';
                            default: return 'autre';
                        }
                    }

                    function autoSelectFirst(selectEl, lockKey) {
                        if (!selectEl) return;
                        // Only skip if user manually selected something AND it's still set
                        if (locks[lockKey] && selectEl.value) return;
                        
                        if (selectEl.options.length > 1) {
                            selectEl.selectedIndex = 1; // first real option
                            selectEl.dispatchEvent(new Event('change'));
                        }
                    }
            
                    let classifyTimeout;
            
                    function classifyText() {
                        const subject = sujetInput.value.trim();
                        const description = descriptionInput.value.trim();
                
                        if (!subject && !description) {
                            previewBox.style.display = 'none';
                            return;
                        }
                
                        clearTimeout(classifyTimeout);
                        classifyTimeout = setTimeout(() => {
                            fetch('ajax_classify_reclamation.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    subject: subject,
                                    description: description
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    console.error('Classification error:', data.error);
                                    return;
                                }
                        
                                // Show preview
                                previewBox.style.display = 'block';
                        
                                // Update values with animation
                                categoryEl.textContent = data.category_label || '-';
                                priorityEl.textContent = data.priority_label || '-';
                                departmentEl.textContent = data.department || '-';
                        
                                // Update confidence
                                const confidence = Math.round(data.confidence || 0);
                                confidenceBar.style.width = confidence + '%';
                                confidenceText.textContent = 'Confiance: ' + confidence + '%';
                        
                                // Color priority based on level
                                const priorityColors = {
                                    'Urgent': 'var(--primary)',
                                    'Élevée': 'var(--primary-light)',
                                    'Moyenne': 'rgba(255,255,255,0.85)',
                                    'Basse': 'rgba(255,255,255,0.6)'
                                };
                                priorityEl.style.color = priorityColors[data.priority_label] || 'var(--primary-light)';

                                // Auto-fill subject if empty
                                if (!subject && sujetInput) {
                                    sujetInput.value = 'Réclamation - ' + (data.category_label || 'Automatique');
                                }

                                // Auto-select type - always re-run on content change
                                if (typeSelect) {
                                    const textForMatch = subject + ' ' + description;
                                    const mapped = mapCategoryToType(data.category || '');
                                    const best = bestEntityMatch(textForMatch);
                                    const chosenType = best ? best.type : mapped;
                                    typeSelect.value = chosenType;
                                    typeSelect.dispatchEvent(new Event('change'));

                                    // Auto-pick relevant entity - always update on content change
                                    if (chosenType === 'mission') {
                                        locks.mission = false;
                                        missionSelect.value = '';
                                        autoSelectByMatch(missionSelect, 'mission', textForMatch);
                                    }
                                    if (chosenType === 'evenement') {
                                        locks.event = false;
                                        eventSelect.value = '';
                                        autoSelectByMatch(eventSelect, 'event', textForMatch);
                                    }
                                    if (chosenType === 'partenaire') {
                                        locks.partner = false;
                                        partnerSelect.value = '';
                                        autoSelectByMatch(partnerSelect, 'partner', textForMatch);
                                    }
                                    if (chosenType === 'utilisateur') {
                                        locks.user = false;
                                        userSelect.value = '';
                                        autoSelectByMatch(userSelect, 'user', textForMatch);
                                    }
                                    if (chosenType === 'technique') {
                                        techniqueInput.value = '';
                                        fillTechnique(description);
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                            });
                        }, 500); // Debounce 500ms
                    }
            
                    // Attach listeners
                    if (sujetInput) sujetInput.addEventListener('input', classifyText);
                    if (descriptionInput) descriptionInput.addEventListener('input', classifyText);
                })();

        (function() {
            const typeSelect = document.getElementById('type_reclamation');
            const blocks = document.querySelectorAll('.conditional-field');
            const requiredByType = {
                mission: ['mission_id'],
                evenement: ['evenement_id'],
                partenaire: ['partenaire_id'],
                utilisateur: ['utilisateur_cible_id'],
                technique: ['technique_detail']
            };

            function updateBlocks() {
                const current = typeSelect ? typeSelect.value : '';
                blocks.forEach(block => {
                    const isActive = block.dataset.block === current;
                    block.classList.toggle('d-none', !isActive);
                });

                Object.keys(requiredByType).forEach(type => {
                    requiredByType[type].forEach(id => {
                        const field = document.getElementById(id);
                        if (field) {
                            // field.required = (type === current);
                        }
                    });
                });
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', updateBlocks);
                updateBlocks();
            }
        })();

        // Clear form after successful submission
        if (document.location.href.includes('addreclamation.php')) {
            const urlParams = new URLSearchParams(window.location.search);
            // Form will auto-reset due to page reload, just ensure all fields are empty
            window.addEventListener('load', function() {
                const form = document.getElementById('reclamationForm');
                if (form) {
                    // Give slight delay to ensure page is fully loaded
                    setTimeout(function() {
                        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], textarea, select');
                        inputs.forEach(input => {
                            if (input.id !== 'email' || !input.value) { // Keep email if it has a value
                                input.value = '';
                            }
                        });
                    }, 100);
                }
            });
        }
    </script>
    <!-- Validation spécifique du formulaire de réclamation -->
    <script src="js/form-validation.js"></script>
</body>

</html>
