<?php

require_once __DIR__ . '/../../Controller/ReclamationController.php';
require_once __DIR__ . '/../../Controller/ResponseController.php';

if (!isset($_GET['id']) || !$_GET['id']) {
    header('Location: listReclamation.php'); exit;
}
$id = intval($_GET['id']);

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$rec = $recCtrl->getReclamation($id);
if (!$rec) { echo "Réclamation introuvable."; exit; }
$responses = $respCtrl->getResponses($id);

// Set page variables
$pageTitle = 'Détails réclamation #' . $rec['id'];
$activePage = 'reclamations';

ob_start();
?>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Réclamation #<?= htmlspecialchars($rec['id']) ?></h5>
  </div>
  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-6">
        <p>Sujet: <?= htmlspecialchars($rec['sujet']) ?></p>
        <p>Email: <?= htmlspecialchars($rec['email']) ?></p>
        <p>Date de création:
          <?php if (!empty($rec['date_creation'])): ?>
            <?= date('d/m/Y H:i', strtotime($rec['date_creation'])) ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </p>
        <p>Statut: 
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
        </p>
      </div>
      <div class="col-md-6">
        <?php if (!empty($rec['telephone'])): ?>
          <p>Téléphone: <?= htmlspecialchars($rec['telephone']) ?></p>
        <?php endif; ?>
        <?php if (!empty($rec['autres'])): ?>
          <p>Autres infos:<br><?= nl2br(htmlspecialchars($rec['autres'])) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-header" style="margin-top: 20px;">
      <h5 class="card-title mb-0">Description</h5>
    </div>
    <div style="padding:15px; background:var(--accent-color); border-radius: 8px; margin-top: 10px;">
      <?= nl2br(htmlspecialchars($rec['description'])) ?>
    </div>

    <div class="card-header" style="margin-top: 20px;">
      <h5 class="card-title mb-0"><i class="fas fa-comments"></i> Réponses (<?= count($responses ?? []) ?>)</h5>
    </div>
    <?php if ($responses): ?>
      <div style="margin-top: 15px;">
        <?php foreach ($responses as $r): ?>
          <div style="padding:15px; background:var(--accent-color); margin-bottom:12px; border-radius: 8px; border-left: 4px solid var(--primary-color);">
            <p style="margin-bottom: 8px;"><?= nl2br(htmlspecialchars($r['contenu'])) ?></p>
            <small style="color: #b0b3c1;"><i class="fas fa-calendar"></i> <?= $r['date_response'] ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p style="margin-top: 15px;">Aucune réponse pour le moment.</p>
    <?php endif; ?>

    <div style="margin-top: 20px;">
      <a class="btn btn-primary" href="listReclamation.php"><i class="fas fa-arrow-left"></i> Retour</a>
      <a class="btn btn-primary" href="response.php?id=<?= $rec['id'] ?>" style="margin-left: 10px;"><i class="fas fa-reply"></i> Ajouter une réponse</a>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
