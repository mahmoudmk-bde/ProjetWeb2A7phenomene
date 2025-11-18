<?php
// View/Back/respond.php
require_once __DIR__ . '/../../Controller/ReclamationController.php';
require_once __DIR__ . '/../../Controller/ResponseController.php';
require_once __DIR__ . '/../../Model/Reponse.php';

if (!isset($_GET['id']) || !$_GET['id']) {
    header('Location: listReclamation.php'); exit;
}
$id = intval($_GET['id']);

$recCtrl = new ReclamationController();
$rec = $recCtrl->getReclamation($id);
if (!$rec) { header('Location: listReclamation.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respCtrl = new ResponseController();
    $respCtrl->addResponse($id, $_POST['response']);
    header('Location: details.php?id=' . $id);
    exit;
}

// Set page variables
$pageTitle = 'Répondre à la réclamation #' . $rec['id'];
$activePage = 'reclamations';

ob_start();
?>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-reply"></i> Répondre à la réclamation #<?= htmlspecialchars($rec['id']) ?></h5>
      </div>
      <div class="card-body">
        <div style="padding:15px; background:var(--accent-color); border-radius: 8px; margin-bottom: 20px;">
          <p style="margin-bottom: 10px;"><strong>Sujet:</strong> <?= htmlspecialchars($rec['sujet']) ?></p>
          <p style="margin-bottom: 10px;"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($rec['description'])) ?></p>
          <p style="margin: 0;"><strong>Email client:</strong> <?= htmlspecialchars($rec['email']) ?></p>
        </div>

        <form method="POST">
          <div class="form-group">
            <label for="response"><strong>Votre réponse</strong></label>
            <textarea class="form-control" id="response" name="response" rows="8" required style="background: var(--accent-color); border: 1px solid #2d3047; color: var(--text-color); resize: vertical;"></textarea>
            <small style="color: #b0b3c1;">Soyez courtois et professionnel dans votre réponse</small>
          </div>
          <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer la réponse</button>
            <a class="btn" href="details.php?id=<?= $rec['id'] ?>" style="background: #6c757d; color: white; padding: 10px 25px; text-decoration: none; border-radius: 25px; margin-left: 10px;"><i class="fas fa-times"></i> Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
      </div>
      <div class="card-body">
        <div class="list-group">
          <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h6 class="mb-1">ID Réclamation</h6>
              <span class="text-primary">#<?= htmlspecialchars($rec['id']) ?></span>
            </div>
          </a>
          <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h6 class="mb-1">Statut</h6>
              <?php
                $statusClass = 'badge-info';
                if ($rec['statut'] === 'Résolu') {
                  $statusClass = 'badge-success';
                } elseif ($rec['statut'] === 'En attente') {
                  $statusClass = 'badge-warning';
                } elseif ($rec['statut'] === 'Rejeté') {
                  $statusClass = 'badge-danger';
                }
              ?>
              <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($rec['statut']) ?></span>
            </div>
          </a>
          <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
              <h6 class="mb-1">Créée le</h6>
              <small><?= date('d/m/Y', strtotime($rec['date_creation'])) ?></small>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
