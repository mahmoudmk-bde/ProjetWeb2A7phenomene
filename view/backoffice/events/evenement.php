<?php
session_start();
// Use project DB config; fallback path relative to this file
if (!class_exists('config')) {
    $dbCfg = __DIR__ . '/../../../db_config.php';
    if (file_exists($dbCfg)) {
        require_once $dbCfg;
    }
}
require_once __DIR__ . '/../../../model/evenementModel.php';

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
<?php require_once 'lang/lang_config.php'; ?>
<?php // include 'assets/layout_top.php'; 
// NOTE: Layout top is likely including the sidebar/header we modified. 
// We need to make sure the language initialization happens before layout is rendered if layout uses it.
// Since we added language config require in index.php at top, and this file is likely standalone or included... 
// actually, evenement.php seems standalone.
// If layout_top.php contains the HTML skeleton, we should ensure the lang attribute is dynamic there too.
// For now, let's translate the content body here.
?>
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4"><?= __('events') ?></h1>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?= __('upcoming_events') ?></h5>
                            <a href="createevent.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <?= __('create_event') ?>
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
                                            <th>Vues</th>
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
                                            <td><?= $event['vues'] ?? 0 ?></td>
                                            <td>
                                                <!-- Actions -->
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-info btn-sm btn-event-details"
                                                            data-toggle="modal"
                                                            data-target="#eventDetailsModal"
                                                            data-titre="<?= htmlspecialchars($event['titre']) ?>"
                                                            data-date="<?= date('d/m/Y', strtotime($event['date_evenement'])) ?>"
                                                            data-heure="<?= substr($event['heure_evenement'], 0, 5) ?>"
                                                            data-lieu="<?= htmlspecialchars($event['lieu']) ?>"
                                                            data-desc="<?= htmlspecialchars($event['description']) ?>"
                                                            data-img="<?= htmlspecialchars(normalize_asset_path($event['image'] ?? '')) ?>"
                                                            data-prix="<?= isset($event['prix']) ? number_format($event['prix'], 2) . ' TND' : 'Gratuit' ?>"
                                                            data-vues="<?= $event['vues'] ?? 0 ?>"
                                                            title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="editevent.php?id=<?= $event['id_evenement'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="participation.php?event_id=<?= $event['id_evenement'] ?>" class="btn btn-secondary btn-sm" title="Gérer Participations">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="evenement.php?action=delete&id=<?= $event['id_evenement'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <?= __('no_events') ?? 'Aucun événement' ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="../Frontoffice/event.php" class="btn btn-secondary">Voir le site public</a>
                    </div>
                </div>
            </div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailsModalLabel">Détails de l'Événement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-5">
                <img src="" id="modal-event-img" class="img-fluid rounded mb-3" alt="Event Image" style="object-fit: cover; width: 100%; height: 200px;" onerror="this.onerror=null; this.src='../../img/logo.png';">
            </div>
            <div class="col-md-7">
                <h3 id="modal-event-titre" class="mb-3"></h3>
                <div class="mb-2">
                    <i class="far fa-calendar-alt text-muted mr-2"></i> <span id="modal-event-date"></span>
                    <i class="far fa-clock text-muted ml-3 mr-2"></i> <span id="modal-event-heure"></span>
                </div>
                <div class="mb-3">
                    <i class="fas fa-map-marker-alt text-muted mr-2"></i> <span id="modal-event-lieu"></span>
                </div>
                <div class="mb-3">
                    <strong>Prix: </strong> <span id="modal-event-prix" class="badge badge-primary"></span>
                    <strong class="ml-3">Vues: </strong> <span id="modal-event-vues"></span>
                </div>
                <div class="p-3 rounded" style="background-color: var(--accent-color); border: 1px solid rgba(255,255,255,0.1);">
                    <p id="modal-event-desc" style="white-space: pre-line; margin-bottom: 0;"></p>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.btn-event-details', function() {
        const btn = $(this);
        
        $('#modal-event-titre').text(btn.data('titre'));
        $('#modal-event-date').text(btn.data('date'));
        $('#modal-event-heure').text(btn.data('heure'));
        $('#modal-event-lieu').text(btn.data('lieu'));
        $('#modal-event-desc').text(btn.data('desc'));
        $('#modal-event-prix').text(btn.data('prix'));
        $('#modal-event-vues').text(btn.data('vues'));
        
        let img = btn.data('img');
        $('#modal-event-img').attr('src', img);
    });
});
</script>

<?php include 'assets/layout_bottom.php'; ?>