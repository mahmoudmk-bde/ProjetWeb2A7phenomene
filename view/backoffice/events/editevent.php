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
        $value = strip_tags($value);
        return $value;
    }
}

// Helper to normalize stored image paths so Backoffice displays the correct image
function normalize_backoffice_image($path) {
    if (empty($path)) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return $path; // keep stored relative path like 'assets/filename.jpg'
}

// Get event ID from URL or POST
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "Événement non trouvé";
    header('Location: evenement.php');
    exit;
}

// Retrieve event data
$eventData = $eventModel->getById($id);

// Verify if event exists
if (!$eventData) {
    $_SESSION['error'] = "Événement non trouvé";
    header('Location: evenement.php');
    exit;
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
    
    // Image handling
    $image = $eventData['image']; // Current image by default
    
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
    
    // Update 
    if ($eventModel->update($id, $titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement, $prix)) {
        $_SESSION['success'] = "Événement modifié avec succès!";
        header('Location: editevent.php?id=' . $id . '&success=1');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la modification de l'événement";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'Événement</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <style>
        body {
            background: linear-gradient(135deg, #1f2235 0%, #2d325a 100%);
            min-height: 100vh;
            color: #fff;
            padding: 20px;
        }

        .edit-card {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .edit-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #fff;
        }

        .edit-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 74, 87, 0.5);
            box-shadow: 0 0 0 3px rgba(255, 74, 87, 0.1);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group select option {
            background: #1f2235;
            color: #fff;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row-2 .form-group {
            margin-bottom: 0;
        }

        .current-image {
            margin-top: 10px;
        }

        .current-image img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            margin-top: 5px;
        }

        .btn-save-pro {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b75 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-save-pro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 74, 87, 0.4);
        }

        .btn-save-pro:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .form-row-2 {
                grid-template-columns: 1fr;
            }

            .edit-card {
                padding: 25px;
            }

            .edit-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
<div class="edit-card">

    <h2 class="edit-title">Modifier l'Événement</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="background: rgba(255, 107, 107, 0.2); border: 1px solid rgba(255, 74, 87, 0.5); color: #ff6b75; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: rgba(52, 211, 153, 0.2); border: 1px solid rgba(16, 185, 129, 0.5); color: #6ee7b7; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="edit-form">

        <input type="hidden" name="id" value="<?= $eventData['id_evenement'] ?>">

        <div class="form-group">
            <label>Titre de l'événement</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($eventData['titre']) ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?= htmlspecialchars($eventData['description']) ?></textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Date de l'événement</label>
                <input type="date" name="date_evenement" value="<?= $eventData['date_evenement'] ?>" required>
            </div>

            <div class="form-group">
                <label>Heure de l'événement</label>
                <input type="time" name="heure_evenement" value="<?= htmlspecialchars($eventData['heure_evenement'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Durée (minutes)</label>
                <input type="number" min="15" step="5" name="duree_minutes" value="<?= htmlspecialchars($eventData['duree_minutes'] ?? '') ?>" placeholder="Ex: 90">
            </div>

            <div class="form-group">
                <label>Lieu</label>
                <input type="text" name="lieu" value="<?= htmlspecialchars($eventData['lieu']) ?>" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Thème</label>
                <select name="id_organisation" required>
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
                <label>Type d'événement</label>
                <select name="type_evenement" id="type_evenement" required>
                    <option value="gratuit" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'gratuit') ? 'selected' : '' ?>>Gratuit</option>
                    <option value="payant" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'payant') ? 'selected' : '' ?>>Payant</option>
                </select>
            </div>
        </div>

        <div class="form-group" id="prix_wrapper" style="display: <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'payant') ? 'block' : 'none' ?>;">
            <label>Prix (TND)</label>
            <input type="number" min="0" step="0.1" name="prix" value="<?= isset($eventData['prix']) ? htmlspecialchars($eventData['prix']) : '' ?>" placeholder="Ex: 10">
            <div class="form-text">Laissez vide ou 0 pour un événement gratuit.</div>
        </div>

        <div class="form-group">
            <label>Image de l'événement</label>
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($eventData['image'])): ?>
                <div class="current-image">
                    <p style="margin-bottom: 10px;">Image actuelle :</p>
                    <img src="<?= normalize_backoffice_image($eventData['image']) ?>" alt="Image actuelle">
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
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Modifier l'Événement</h1>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?php if (isset($eventData) && !empty($eventData['titre'])): ?>
                                    Modifier "<?= htmlspecialchars($eventData['titre']) ?>"
                                <?php else: ?>
                                    Modifier l'Événement
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($eventData) && !empty($eventData)): ?>
                            <div id="event-edit-errors"></div>
                            <form id="event-edit-form" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $eventData['id_evenement'] ?>">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de l'événement *</label>
                                    <input type="text" class="form-control" id="titre" name="titre" 
                                           value="<?= htmlspecialchars($eventData['titre']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4"><?= htmlspecialchars($eventData['description']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_evenement" class="form-label">Date de l'événement *</label>
                                    <input type="date" class="form-control" id="date_evenement" name="date_evenement" 
                                           value="<?= $eventData['date_evenement'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="heure_evenement" class="form-label">Heure de l'événement</label>
                                    <input type="time" class="form-control" id="heure_evenement" name="heure_evenement" 
                                           value="<?= htmlspecialchars($eventData['heure_evenement'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="duree_minutes" class="form-label">Durée (minutes)</label>
                                    <input type="number" min="15" step="5" class="form-control" id="duree_minutes" name="duree_minutes" 
                                           value="<?= htmlspecialchars($eventData['duree_minutes'] ?? '') ?>" placeholder="Ex: 90">
                                </div>

                                <div class="mb-3">
                                    <label for="lieu" class="form-label">Lieu *</label>
                                    <input type="text" class="form-control" id="lieu" name="lieu" 
                                           value="<?= htmlspecialchars($eventData['lieu']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Type d'événement *</label>
                                    <select class="form-control" id="type_evenement" name="type_evenement">
                                        <option value="gratuit" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'gratuit') ? 'selected' : '' ?>>Gratuit</option>
                                        <option value="payant" <?= (isset($eventData['type_evenement']) && $eventData['type_evenement'] === 'payant') ? 'selected' : '' ?>>Payant</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="prix_wrapper">
                                    <label for="prix" class="form-label">Prix (TND)</label>
                                    <input type="number" min="0" step="0.1" class="form-control" id="prix" name="prix"
                                           value="<?= isset($eventData['prix']) ? htmlspecialchars($eventData['prix']) : '' ?>" placeholder="Ex: 10">
                                    <div class="form-text">Laissez vide ou 0 pour un événement gratuit.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="id_organisation" class="form-label">Thème *</label>
                                    <select class="form-control" id="id_organisation" name="id_organisation">
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
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image de l'événement</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <?php if (!empty($eventData['image'])): ?>
                                    <div class="mt-2">
                                        <p>Image actuelle :</p>
                                        <img src="<?= normalize_backoffice_image($eventData['image']) ?>" alt="Image actuelle" 
                                             style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Mettre à jour l'événement</button>
                                <a href="evenement.php" class="btn btn-secondary">Annuler</a>
                            </form>
                            <?php else: ?>
                            <div class="alert alert-danger">
                                Événement non trouvé ou données manquantes.
                                <a href="evenement.php" class="alert-link">Retour à la liste des événements</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<?php include 'assets/layout_bottom.php'; ?>