<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';

$eventModel = new EvenementModel();

// Helper to normalize stored image paths so Backoffice displays the correct image
function normalize_backoffice_image($path) {
    if (empty($path)) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return normalize_asset_path($path, '');
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
        $uploadDir = '../../uploads/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['image']['name'];
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = 'uploads/events/' . $fileName;
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