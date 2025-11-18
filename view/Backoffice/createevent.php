<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';

$eventModel = new EvenementModel();

// Handle form submission
if ($_POST) {
    $titre = secure_data($_POST['titre']);
    $description = secure_data($_POST['description']);
    $date_evenement = $_POST['date_evenement'];
    $lieu = secure_data($_POST['lieu']);
    $id_organisation = $_POST['id_organisation'];
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
    
    if ($eventModel->create($titre, $description, $date_evenement, $lieu, $image, $id_organisation)) {
        $_SESSION['success'] = "Événement créé avec succès!";
        header('Location: createevent.php?success=1');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'événement";
    }
}
?>
<!DOCTYPE html>>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement - ENGAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">ENGAGE - Administration</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Créer un Événement</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Nouvel Événement</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre de l'événement *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_evenement" class="form-label">Date de l'événement *</label>
                        <input type="date" class="form-control" id="date_evenement" name="date_evenement" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lieu" class="form-label">Lieu *</label>
                        <input type="text" class="form-control" id="lieu" name="lieu" required>
                        <div class="form-text">Précisez si c'est en ligne ou l'adresse physique</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_organisation" class="form-label">Organisation *</label>
                        <select class="form-control" id="id_organisation" name="id_organisation" required>
                            <option value="">Sélectionnez une organisation</option>
                            <option value="1">Association Gaming Solidarité</option>
                            <option value="2">Hôpital des Jeunes</option>
                            <option value="3">École du Gaming</option>
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
</body>
</html>