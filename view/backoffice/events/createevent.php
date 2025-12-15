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

$eventModel = new EvenementModel();

// Fallback sanitization helper if not globally defined
if (!function_exists('secure_data')) {
    function secure_data($value) {
        if (is_array($value)) {
            return array_map('secure_data', $value);
        }
        $value = trim($value);
        // Remove HTML tags and normalize whitespace
        $value = strip_tags($value);
        return $value;
    }
}

// Small helper in case we need to normalize paths later
function normalize_backoffice_image_create($path) {
    if (empty($path)) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return $path; // keep stored relative path like 'assets/filename.jpg'
}


if ($_POST) {
    $titre = secure_data($_POST['titre']);
    $description = secure_data($_POST['description']);
    $date_evenement = $_POST['date_evenement'];
    $heure_evenement = isset($_POST['heure_evenement']) && $_POST['heure_evenement'] !== '' ? $_POST['heure_evenement'] : null;
    $duree_minutes = isset($_POST['duree_minutes']) && $_POST['duree_minutes'] !== '' ? (int) $_POST['duree_minutes'] : null;
    $lieu = secure_data($_POST['lieu']);
    $id_organisation = $_POST['id_organisation'];
    $type_evenement = isset($_POST['type_evenement']) && $_POST['type_evenement'] === 'payant' ? 'payant' : 'gratuit';
    $prix = null;
    if ($type_evenement === 'payant' && isset($_POST['prix']) && $_POST['prix'] !== '') {
        $prix = (float) $_POST['prix'];
    }
    $image = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = __DIR__ . '/assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['image']['name'];
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = 'assets/' . $fileName;
        }
    }
    
    $created_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    if ($eventModel->create($titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement, $prix, $created_by)) {
        $_SESSION['success'] = "🎉 Événement créé avec succès !";
        header('Location: evenement.php');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'événement";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un événement</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../frontoffice/css/all.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1f2235 0%, #2d325a 100%);
            min-height: 100vh;
            color: #fff;
        }
        .container {
            padding: 40px 20px;
        }
    </style>
</head>

<body>
<div class="container">

<div class="form-card">

    <h2>Créer un nouvel événement</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group-modern">
            <input type="text" name="titre" placeholder=" " required>
            <label>Titre de l'événement</label>
        </div>

        <div class="form-group-modern">
            <input type="text" name="lieu" placeholder=" " required>
            <label>Lieu de l'événement</label>
        </div>
        
        <div class="form-group-modern">
            <label style="position:static; display:block; margin-bottom:5px; color:#ccc; font-size:14px;">
                Dates de l'événement
            </label>
            <input type="date" name="date_evenement" required style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333; margin-bottom:10px;">
            <input type="time" name="heure_evenement" style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333;">
        </div>

        <div class="form-group-modern">
            <input type="number" name="duree_minutes" placeholder=" " min="15" step="5">
            <label>Durée (minutes)</label>
        </div>

        <div class="form-group-modern">
            <textarea name="description" placeholder=" " rows="4"></textarea>
            <label>Description de l'événement</label>
        </div>

        <div class="form-group-modern">
            <label style="position:static; display:block; margin-bottom:5px; color:#ccc; font-size:14px;">
                Thème de l'événement
            </label>
            <select name="id_organisation" style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333;" required>
                <option value="">Sélectionnez un thème</option>
                <option value="1">Sport</option>
                <option value="2">Éducation</option>
                <option value="3">Esport</option>
                <option value="4">Création</option>
                <option value="5">Prévention</option>
                <option value="6">Coaching</option>
                <option value="7">Compétition</option>
            </select>
        </div>

        <div class="form-group-modern">
            <label style="position:static; display:block; margin-bottom:5px; color:#ccc; font-size:14px;">
                Type d'événement
            </label>
            <select name="type_evenement" style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333;" required onchange="document.getElementById('prix_wrapper').style.display = this.value === 'payant' ? 'block' : 'none';">
                <option value="gratuit">Gratuit</option>
                <option value="payant">Payant</option>
            </select>
        </div>

        <div class="form-group-modern" id="prix_wrapper" style="display:none;">
            <input type="number" name="prix" placeholder=" " min="0" step="0.1">
            <label>Prix (TND)</label>
        </div>

        <div class="form-group-modern">
            <label style="position:static; display:block; margin-bottom:5px; color:#ccc; font-size:14px;">
                Image de l'événement
            </label>
            <input type="file" name="image" accept="image/*" style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333;">
        </div>

        <button class="btn-submit">Créer l'événement</button>

    </form>

</div>

</div>

</div>

<script src="../assets/js/back.js"></script>
</body>
</html>