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
// Simple pagination to match missions list layout
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 2;
$totalEvents = is_array($events) ? count($events) : 0;
$totalPages = $totalEvents > 0 ? (int)ceil($totalEvents / $perPage) : 1;
$offset = ($page - 1) * $perPage;
$pageEvents = array_slice($events, $offset, $perPage);
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
<?php if (!isset($_GET['embed'])) { include 'assets/layout_top.php'; } ?>
<?php $embed = isset($_GET['embed']) && $_GET['embed'] == '1'; ?>
<?php if ($embed): ?>
<style>
    /* Clean up embedded view inside dashboard iframe */
    .public-link { display: none !important; }
    .modal, .modal-backdrop { display: none !important; }
    hr { display: none !important; }
    .event-grid { gap: 16px; }
    .event-card { margin-top: 4px; margin-bottom: 8px; }
</style>
<?php endif; ?>

            <div class="row mt-3" <?= isset($_GET['embed']) ? 'style="margin-left:0;padding:20px"' : '' ?> >
                <div class="col-12">
                    <div class="missions-header" style="margin-bottom: 20px;">
                        <h2 class="text-white">🎯 Gestion des Événements</h2>
                        <a href="createevent.php<?= isset($_GET['embed']) ? '?embed=1' : '' ?>" class="add-mission-btn">
                            <i class="fas fa-plus"></i> Nouvelle Événement
                        </a>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    

                    <?php if (!empty($pageEvents)): ?>
                    <style>
                        .missions-header { display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap; }
                        .add-mission-btn { background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); border:none; border-radius:25px; padding:12px 25px; font-weight:600; color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition: all 0.3s; }
                        .add-mission-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4); color:white; }
                        .event-grid { display: grid; grid-template-columns: repeat(2, minmax(360px, 1fr)); gap: 24px; align-items: start; }
                        .event-card { background: var(--accent-color); padding: 18px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 10px 24px rgba(255, 0, 90, 0.12); transition: all 0.3s ease; position: relative; display: flex; flex-direction: column; }
                        .event-card::after { content: ""; position: absolute; inset: -2px; border-radius: 18px; box-shadow: 0 0 0 1px rgba(255,74,87,0.18), 0 0 40px rgba(255,0,90,0.08); pointer-events: none; }
                        .event-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3); border-color: var(--primary-color); }
                        .event-header { margin-bottom: 8px; display:flex; justify-content:space-between; align-items:center; }
                        .event-title { font-size: 0.95rem; font-weight: 700; color: var(--primary-color); margin: 0 0 4px 0; }
                        .badge-level { background: var(--secondary-color); padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
                        .event-dates { display:flex; flex-direction:column; gap:4px; margin-bottom:8px; font-size:0.75rem; }
                        .date-info { display:flex; align-items:center; gap:4px; color: var(--text-muted); }
                        .date-info i { color: var(--primary-color); font-size:0.8rem; }
                        .event-details { display:flex; flex-direction:column; gap:6px; margin-bottom:8px; flex:1; }
                        .detail-item { display:flex; align-items:center; gap:6px; padding:6px; background: var(--secondary-color); border-radius:6px; font-size:0.75rem; }
                        .detail-item i { color: var(--primary-color); width:12px; text-align:center; flex-shrink:0; font-size:0.75rem; }
                        .detail-label { color: var(--text-muted); font-size:0.65rem; display:block; }
                        .detail-value { color: var(--text-color); font-weight:600; font-size:0.8rem; }
                        .mission-likes-info { margin: 12px 0; padding: 10px; background: rgba(255, 74, 87, 0.12); border-radius: 8px; border-left: 3px solid #ff4a57; display: flex; align-items: center; gap: 8px; }
                        .mission-actions { display:flex; justify-content:center; gap:8px; margin-top:auto; padding-top:10px; border-top:1px solid var(--border-color); }
                        .btn-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; transition: all 0.3s ease; border:none; font-size:0.95rem; cursor:pointer; }
                        .btn-icon:hover { transform: translateY(-3px) scale(1.1); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
                        .btn-candidatures { background: linear-gradient(45deg, #007bff, #0056b3); color:white; }
                        .btn-modifier { background: linear-gradient(45deg, #ffc107, #e0a800); color:#212529; }
                        .btn-supprimer { background: linear-gradient(45deg, #dc3545, #c82333); color:white; }
                        /* Pagination */
                        .pagination { display: flex; gap: 6px; justify-content: center; padding-left: 0; list-style: none; }
                        .pagination .page-item .page-link { color: #212529; background: #fff; border: 1px solid #e6e6e6; padding: 8px 12px; border-radius: 6px; min-width: 40px; text-align: center; transition: all .12s ease; }
                        .pagination .page-item.active .page-link { background: linear-gradient(45deg, #ff4a57, #ff6b6b); color: #fff; border-color: rgba(0,0,0,0.06); box-shadow: 0 6px 14px rgba(255,74,87,0.16); }
                        .pagination .page-item .page-link:hover { transform: translateY(-3px); }
                        .pagination .page-item.disabled .page-link { opacity: .5; pointer-events: none; }
                        @media (max-width: 992px) { .event-grid { grid-template-columns: repeat(1, 1fr); } }
                    </style>
                    <div class="event-grid">
                        <?php foreach ($pageEvents as $event): ?>
                        <div class="event-card">
                            <div class="event-header">
                                <div>
                                    <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                                    <span class="badge-level">Événement</span>
                                </div>
                            </div>
                            <div class="event-dates">
                                <div class="date-info">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></span>
                                </div>
                                <?php if (!empty($event['heure_evenement'])): ?>
                                <div class="date-info">
                                    <i class="far fa-clock"></i>
                                    <span><?= htmlspecialchars(substr($event['heure_evenement'],0,5)) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="event-details">
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <div class="detail-label">Lieu</div>
                                        <div class="detail-value"><?= htmlspecialchars($event['lieu']) ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($event['duree_minutes'])): ?>
                                <div class="detail-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    <div>
                                        <div class="detail-label">Durée</div>
                                        <div class="detail-value"><?= (int)$event['duree_minutes'] ?> min</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($event['description'])): ?>
                                <div class="detail-item" style="grid-column: 1 / -1;">
                                    <i class="fas fa-file-alt"></i>
                                    <div>
                                        <div class="detail-label">Description</div>
                                        <div class="detail-value" style="font-weight: normal; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars(substr($event['description'], 0, 50)) ?>
                                            <?= strlen($event['description']) > 50 ? '...' : '' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="mission-likes-info">
                                <i class="fas fa-heart" style="color: #ff4a57; font-size: 1rem;"></i>
                                <span style="color: var(--text-color); font-weight: 600; font-size: 0.9rem;">
                                    <?= (int)$eventModel->countParticipants($event['id_evenement']) ?> participants • <?= (int)($event['vues'] ?? 0) ?> vues
                                </span>
                            </div>
                            <div class="mission-actions">
                                <a href="participation.php?event_id=<?= $event['id_evenement'] ?><?= isset($_GET['embed']) ? '&embed=1' : '' ?>" class="btn-icon btn-candidatures" title="Participants"><i class="fas fa-users"></i></a>
                                <a href="editevent.php?id=<?= $event['id_evenement'] ?><?= isset($_GET['embed']) ? '&embed=1' : '' ?>" class="btn-icon btn-modifier" title="Éditer"><i class="fas fa-edit"></i></a>
                                <a href="evenement.php?action=delete&id=<?= $event['id_evenement'] ?>" class="btn-icon btn-supprimer" title="Supprimer" onclick="return confirm('Supprimer cet événement ?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0"><?= __('no_events') ?? 'Aucun événement' ?></div>
                    <?php endif; ?>

                    <?php if ($totalPages > 1): ?>
                    <div class="text-center mt-4">
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= max(1, $page-1) ?><?= $embed ? '&embed=1' : '' ?>" aria-label="Précédent">&laquo;</a>
                                </li>
                                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $p ?><?= $embed ? '&embed=1' : '' ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= min($totalPages, $page+1) ?><?= $embed ? '&embed=1' : '' ?>" aria-label="Suivant">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

<?php if (!$embed): ?>
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
<?php endif; ?>

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