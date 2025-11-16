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

ob_start();

$pageTitle = 'Détails réclamation #' . $rec['id'];
?>

<div class="card">

 

  <p><strong>Sujet:</strong> <?= htmlspecialchars($rec['sujet']) ?></p>

  <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($rec['description'])) ?></p>

  <p><strong>Email:</strong> <?= htmlspecialchars($rec['email']) ?></p>

  <p><strong>Date de création:</strong>
    <?php if (!empty($rec['date_creation'])): ?>
      <?= date('d/m/Y H:i', strtotime($rec['date_creation'])) ?>
    <?php else: ?>
      -
    <?php endif; ?>
  </p>

  <p><strong>Statut:</strong> <span style="font-weight:600;"><?= htmlspecialchars($rec['statut']) ?></span></p>

  <?php if (!empty($rec['autres']) || !empty($rec['telephone'])): ?>
    <div style="margin-top:8px;padding:8px;background:#fafafa;border-radius:6px;">
      <?php if (!empty($rec['telephone'])): ?>
        <p><strong>Téléphone:</strong> <?= htmlspecialchars($rec['telephone']) ?></p>
      <?php endif; ?>
      <?php if (!empty($rec['autres'])): ?>
        <p><strong>Autres infos:</strong> <?= nl2br(htmlspecialchars($rec['autres'])) ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <h3>Réponses</h3>
  <?php if ($responses): ?>
    <?php foreach ($responses as $r): ?>
      <div style="padding:10px;background:#f9f9f9;margin-bottom:8px;border-radius:6px;">
        <?= nl2br(htmlspecialchars($r['contenu'])) ?>
        <div style="font-size:11px;color:#666; margin-top:6px;"><?= $r['date_response'] ?></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p><em>Aucune réponse pour le moment.</em></p>
  <?php endif; ?>

  <a class="btn" href="listReclamation.php">Retour</a>
  <a class="btn" style="background:#2d89ef;color:#fff" href="response.php?id=<?= $rec['id'] ?>">Répondre</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
