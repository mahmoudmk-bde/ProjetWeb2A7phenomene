<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load project DB config
if (!class_exists('config')) {
    $dbCfg = __DIR__ . '/../../../db_config.php';
    if (file_exists($dbCfg)) {
        require_once $dbCfg;
    }
}
require_once __DIR__ . '/../../../model/evenementModel.php';

$eventModel = new EvenementModel();

// Fallback sanitization helper
if (!function_exists('secure_data')) {
    function secure_data($value) {
        if (is_array($value)) {
            return array_map('secure_data', $value);
        }
        $value = trim($value);
        $value = strip_tags($value);
        return $value;
    }
}

// 1) Si le formulaire est soumis (POST) → on met à jour puis on redirige
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
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
    
    // Get current event data for image
    $currentEvent = $eventModel->getById($id);
    $image = $currentEvent['image']; // Keep current image by default
    
    // Handle new image upload
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
    
    // Update event
    $eventModel->update($id, $titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement, $prix);
    
    header('Location: evenement.php');
    exit;
}

// 2) Sinon (GET) → on récupère l'événement à partir de l'id
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID d'événement manquant.");
}

$eventData = $eventModel->getById($id);

if (!$eventData) {
    die("Événement introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier événement</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
</head>

<body>
<div class="edit-card">

    <h2 class="edit-title">Modifier l'événement</h2>

    <form method="POST" enctype="multipart/form-data" class="edit-form">

        <input type="hidden" name="id" value="<?= htmlspecialchars($eventData['id_evenement']) ?>">

        <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($eventData['titre']) ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($eventData['description']) ?></textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Date de l'événement</label>
                <input type="date" name="date_evenement" value="<?= htmlspecialchars($eventData['date_evenement']) ?>">
            </div>

            <div class="form-group">
                <label>Heure</label>
                <input type="time" name="heure_evenement" value="<?= htmlspecialchars($eventData['heure_evenement'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Durée (min.)</label>
                <input type="number" min="15" step="5" name="duree_minutes" value="<?= htmlspecialchars($eventData['duree_minutes'] ?? '') ?>" placeholder="Ex: 90">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Lieu</label>
                <input type="text" name="lieu" value="<?= htmlspecialchars($eventData['lieu']) ?>">
            </div>

            <div class="form-group">
                <label>Thème</label>
                <select name="id_organisation">
                    <option value="">Sélectionnez un thème</option>
                    <option value="1" <?= $eventData['id_organisation'] == 1 ? 'selected' : '' ?>>Sport</option>
                    <option value="2" <?= $eventData['id_organisation'] == 2 ? 'selected' : '' ?>>Éducation</option>
                    <option value="3" <?= $eventData['id_organisation'] == 3 ? 'selected' : '' ?>>Esport</option>
                    <option value="4" <?= $eventData['id_organisation'] == 4 ? 'selected' : '' ?>>Création</option>
                    <option value="5" <?= $eventData['id_organisation'] == 5 ? 'selected' : '' ?>>Prévention</option>
                    <option value="6" <?= $eventData['id_organisation'] == 6 ? 'selected' : '' ?>>Coaching</option>
                    <option value="7" <?= $eventData['id_organisation'] == 7 ? 'selected' : '' ?>>Compétition</option>
                </select>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type_evenement" id="type_evenement">
                    <option value="gratuit" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'gratuit') ? 'selected' : '' ?>>Gratuit</option>
                    <option value="payant" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'payant') ? 'selected' : '' ?>>Payant</option>
                </select>
            </div>
        </div>

        <div class="form-group" id="prix_wrapper" style="display: <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'payant') ? 'block' : 'none' ?>;">
            <label>Prix (TND)</label>
            <input type="number" min="0" step="0.1" name="prix" value="<?= isset($eventData['prix']) ? htmlspecialchars($eventData['prix']) : '' ?>" placeholder="Ex: 10">
        </div>

        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($eventData['image'])): ?>
                <div style="margin-top: 10px;">
                    <p style="margin-bottom: 10px; color: rgba(255,255,255,0.7); font-size: 13px;">Image actuelle :</p>
                    <img src="<?= htmlspecialchars($eventData['image']) ?>" alt="Image actuelle" 
                         style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2);">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-save-pro">💾 Mettre à jour</button>

    </form>

</div>

<script>
    const typeSelect = document.getElementById('type_evenement');
    const prixWrapper = document.getElementById('prix_wrapper');

    function updatePrixVisibility() {
        if (typeSelect.value === 'payant') {
            prixWrapper.style.display = 'block';
        } else {
            prixWrapper.style.display = 'none';
        }
    }

    typeSelect.addEventListener('change', updatePrixVisibility);
</script>

</body>
</html>