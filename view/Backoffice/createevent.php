<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';

$eventModel = new EvenementModel();

// Small helper in case we need to normalize paths later
function normalize_backoffice_image_create($path) {
    if (empty($path)) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    if ($path[0] === '/') return $path;
    return '/gamingroom/' . ltrim($path, '/');
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
    
    if ($eventModel->create($titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement, $prix)) {
        $_SESSION['success'] = "Événement créé avec succès!";
        header('Location: createevent.php?success=1');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'événement";
    }
}
?>
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Créer un Événement</h1>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card dashboard-card">
                        <div class="card-header">
                            <h5 class="mb-0">Nouvel Événement</h5>
                        </div>
                        <div class="card-body">
                            <div id="event-create-errors"></div>
                            <form id="event-create-form" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de l'événement *</label>
                                    <input type="text" class="form-control" id="titre" name="titre">
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="date_evenement" class="form-label">Date de l'événement *</label>
                                    <input type="date" class="form-control" id="date_evenement" name="date_evenement">
                                </div>

                                <div class="mb-3">
                                    <label for="heure_evenement" class="form-label">Heure de l'événement</label>
                                    <input type="time" class="form-control" id="heure_evenement" name="heure_evenement">
                                </div>

                                <div class="mb-3">
                                    <label for="duree_minutes" class="form-label">Durée (minutes)</label>
                                    <input type="number" min="15" step="5" class="form-control" id="duree_minutes" name="duree_minutes" placeholder="Ex: 90">
                                </div>

                                <div class="mb-3">
                                    <label for="lieu" class="form-label">Lieu *</label>
                                    <input type="text" class="form-control" id="lieu" name="lieu">
                                    <div class="form-text">Précisez si c'est en ligne ou l'adresse physique</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Type d'événement *</label>
                                    <select class="form-control" id="type_evenement" name="type_evenement">
                                        <option value="gratuit">Gratuit</option>
                                        <option value="payant">Payant</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="prix_wrapper">
                                    <label for="prix" class="form-label">Prix (TND)</label>
                                    <input type="number" min="0" step="0.1" class="form-control" id="prix" name="prix" placeholder="Ex: 10">
                                    <div class="form-text">Laissez vide ou 0 pour un événement gratuit.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="id_organisation" class="form-label">Thème *</label>
                                    <select class="form-control" id="id_organisation" name="id_organisation">
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

                                <div class="mb-3">
                                    <label for="image" class="form-label">Image de l'événement</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary">Créer l'événement</button>
                                <a href="evenement.php" class="btn btn-secondary">Annuler</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

<?php include 'assets/layout_bottom.php'; ?>