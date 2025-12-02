<?php
session_start();
require_once '../../config.php';
require_once '../../model/evenementModel.php';
require_once '../../model/participationModel.php';

$eventModel = new EvenementModel();
$participationModel = new ParticipationModel();

// Get event ID from URL
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    $_SESSION['error'] = "Événement non spécifié";
    header('Location: evenement.php');
    exit;
}

// Get event data
$event = $eventModel->getById($event_id);

if (!$event) {
    $_SESSION['error'] = "Événement non trouvé";
    header('Location: evenement.php');
    exit;
}

// Handle status update
if (isset($_POST['action']) && $_POST['action'] == 'update_status' && isset($_POST['participation_id'])) {
    $participation_id = $_POST['participation_id'];
    $status = $_POST['status'] ?? 'en attente';
    
    if ($participationModel->updateStatus($participation_id, $status)) {
        $_SESSION['success'] = "Statut de participation mis à jour!";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du statut";
    }
    header('Location: participation.php?event_id=' . $event_id);
    exit;
}

// Get all participations for this event
$participations = $participationModel->getEventParticipations($event_id);
?>
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Gestion des Participations</h1>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Participations pour : <?= htmlspecialchars($event['titre']) ?></h5>
                            <a href="evenement.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour aux événements
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($participations)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Volontaire</th>
                                            <th>Email</th>
                                            <th>Date Participation</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participations as $participation): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($participation['prenom'] . ' ' . $participation['nom']) ?></td>
                                            <td><?= htmlspecialchars($participation['email']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($participation['date_participation'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $participation['statut'] == 'acceptée' ? 'success' : 
                                                    ($participation['statut'] == 'en attente' ? 'warning' : 'danger') 
                                                ?>">
                                                    <?= $participation['statut'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($participation['statut'] == 'en attente'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                        <input type="hidden" name="status" value="acceptée">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Accepter
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                        <input type="hidden" name="status" value="refusée">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> Refuser
                                                        </button>
                                                    </form>
                                                <?php elseif ($participation['statut'] == 'acceptée'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                        <input type="hidden" name="status" value="refusée">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> Refuser
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                        <input type="hidden" name="status" value="acceptée">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Accepter
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                Aucune participation pour cet événement pour le moment.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<?php include 'assets/layout_bottom.php'; ?>