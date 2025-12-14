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

// Pre-process images
$event_img = !empty($event['image']) ? $event['image'] : 'img/default-event.jpg';
if (function_exists('normalize_asset_path')) {
    $event_img = normalize_asset_path($event_img);
}
?>
<?php include 'assets/layout_top.php'; ?>

<style>
    .event-header-card {
        background: #1f2235; /* Dark theme card bg */
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .event-header-content {
        display: flex;
        gap: 20px;
        padding: 20px;
    }
    .event-thumb {
        width: 150px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
    }
    .event-info h2 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #ff4a57;
    }
    .event-meta {
        display: flex;
        gap: 15px;
        font-size: 14px;
        color: #b0b3c1;
    }
    .dashboard-card {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Gestion des Participations</h1>

                    <div class="event-header-card">
                        <div class="event-header-content">
                            <img src="<?= htmlspecialchars($event_img) ?>" alt="Event" class="event-thumb">
                            <div class="event-info">
                                <h2><?= htmlspecialchars($event['titre']) ?></h2>
                                <div class="event-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($event['date_evenement'])) ?></span>
                                    <span><i class="far fa-clock"></i> <?= substr($event['heure_evenement'], 0, 5) ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lieu']) ?></span>
                                </div>
                                <div class="mt-2 text-muted" style="font-size: 13px;">
                                    <?= htmlspecialchars(substr($event['description'], 0, 150)) . (strlen($event['description']) > 150 ? '...' : '') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Liste des Participants</h5>
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
                                            <th>Date / Qte</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participations as $participation): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($participation['prenom'] . ' ' . $participation['nom']) ?>
                                                <?php if(!empty($participation['gamer_tag'])): ?>
                                                    <br><small class="text-muted">@<?= htmlspecialchars($participation['gamer_tag']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($participation['email']) ?></td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($participation['date_participation'])) ?>
                                                <br>
                                                <small class="text-muted">Qte: <?= $participation['quantite'] ?? 1 ?></small>
                                            </td>
                                            <td>
                                                <?php if(!empty($participation['montant_total']) && $participation['montant_total'] > 0): ?>
                                                    <?= number_format($participation['montant_total'], 2) ?> TND
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Gratuit</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $participation['statut'] == 'acceptée' ? 'success' : 
                                                    ($participation['statut'] == 'en attente' ? 'warning' : 'danger') 
                                                ?>">
                                                    <?= $participation['statut'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Action Buttons -->
                                                <div class="btn-group" role="group">
                                                    <!-- Info Button with Data Attributes -->
                                                    <button type="button" class="btn btn-info btn-sm btn-view-details" 
                                                            data-toggle="modal" 
                                                            data-target="#detailsModal"
                                                            data-nom="<?= htmlspecialchars($participation['prenom'] . ' ' . $participation['nom']) ?>"
                                                            data-email="<?= htmlspecialchars($participation['email']) ?>"
                                                            data-date="<?= date('d/m/Y H:i', strtotime($participation['date_participation'])) ?>"
                                                            data-statut="<?= $participation['statut'] ?>"
                                                            data-quantite="<?= $participation['quantite'] ?? 1 ?>"
                                                            data-montant="<?= $participation['montant_total'] ?? 0 ?>"
                                                            data-mode="<?= htmlspecialchars($participation['mode_paiement'] ?? '-') ?>"
                                                            data-ref="<?= htmlspecialchars($participation['reference_paiement'] ?? '-') ?>"
                                                            title="Voir Détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <?php if ($participation['statut'] == 'en attente'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                            <input type="hidden" name="status" value="acceptée">
                                                            <button type="submit" class="btn btn-success btn-sm ml-1" title="Accepter">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                            <input type="hidden" name="status" value="refusée">
                                                            <button type="submit" class="btn btn-danger btn-sm ml-1" title="Refuser">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php elseif ($participation['statut'] == 'acceptée'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                            <input type="hidden" name="status" value="refusée">
                                                            <button type="submit" class="btn btn-danger btn-sm ml-1" title="Annuler/Refuser">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="participation_id" value="<?= $participation['id_participation'] ?>">
                                                            <input type="hidden" name="status" value="acceptée">
                                                            <button type="submit" class="btn btn-success btn-sm ml-1" title="Réactiver">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Détails de la Participation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <dl class="row">
            <dt class="col-sm-4">Volontaire</dt>
            <dd class="col-sm-8" id="modal-nom"></dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8" id="modal-email"></dd>

            <dt class="col-sm-4">Date</dt>
            <dd class="col-sm-8" id="modal-date"></dd>

            <dt class="col-sm-4">Statut</dt>
            <dd class="col-sm-8" id="modal-statut"></dd>

            <dt class="col-sm-12"><hr></dt>

            <dt class="col-sm-4">Quantité</dt>
            <dd class="col-sm-8" id="modal-quantite"></dd>

            <dt class="col-sm-4">Montant Total</dt>
            <dd class="col-sm-8" id="modal-montant"></dd>

            <dt class="col-sm-4">Mode Paiement</dt>
            <dd class="col-sm-8" id="modal-mode"></dd>

            <dt class="col-sm-4">Référence</dt>
            <dd class="col-sm-8 text-monospace" id="modal-ref"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fill modal on button click - using event delegation for safety
    $(document).on('click', '.btn-view-details', function() {
        const btn = $(this);
        
        $('#modal-nom').text(btn.data('nom') || '-');
        $('#modal-email').text(btn.data('email') || '-');
        $('#modal-date').text(btn.data('date') || '-');
        $('#modal-statut').text(btn.data('statut') || '-');
        $('#modal-quantite').text(btn.data('quantite') || '-');
        
        const montant = parseFloat(btn.data('montant'));
        $('#modal-montant').text(montant > 0 ? montant.toFixed(2) + ' TND' : 'Gratuit');
        
        $('#modal-mode').text(btn.data('mode') || '-');
        $('#modal-ref').text(btn.data('ref') || '-');
    });
});
</script>

<?php include 'assets/layout_bottom.php'; ?>