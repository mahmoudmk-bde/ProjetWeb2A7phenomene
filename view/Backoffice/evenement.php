<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';

// Simuler une session d'administration si nécessaire
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
}

$eventModel = new EvenementModel();

// Gérer l'action de suppression
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
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Gestion des Événements</h1>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card dashboard-card">
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
                                            <th>Thème</th>
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
            </div>

<?php include 'assets/layout_bottom.php'; ?>