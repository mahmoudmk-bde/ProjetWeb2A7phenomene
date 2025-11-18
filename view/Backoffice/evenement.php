<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';

// Simulate admin session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
}

$eventModel = new EvenementModel();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($eventModel->delete($_GET['id'])) {
        $_SESSION['success'] = "Événement supprimé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'événement";
    }
    header('Location: evenement.php');
    exit;
}

// Get all events
$events = $eventModel->getAllEvents();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements - ENGAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">ENGAGE - Administration</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Gestion des Événements</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Événements</h5>
                <a href="createevent.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nouvel Événement
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($events)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Organisation</th>
                                <th>Participants</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['titre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></td>
                                <td><?= htmlspecialchars($event['lieu']) ?></td>
                                <td><?= htmlspecialchars($event['organisation_nom'] ?? 'N/A') ?></td>
                                <td><?= $eventModel->countParticipants($event['id_evenement']) ?></td>
                                <td>
                                    <a href="editevent.php?id=<?= $event['id_evenement'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="participation.php?event_id=<?= $event['id_evenement'] ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="evenement.php?action=delete&id=<?= $event['id_evenement'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    Aucun événement créé pour le moment.
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="../Frontoffice/evenement.php" class="btn btn-secondary">Voir le site public</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>