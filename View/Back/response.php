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
    header('Location: listReclamation.php');
    exit;
}

ob_start();
?>
<div class="card">
  <h2>Répondre à la réclamation #<?= $rec['id'] ?></h2>
  <p><strong>Sujet:</strong> <?= htmlspecialchars($rec['sujet']) ?></p>
  <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($rec['description'])) ?></p>

  <form method="POST">
    <div>
      <label>Votre réponse</label><br>
      <textarea name="response" rows="6" style="width:100%;" required></textarea>
    </div>
    <br>
    <button class="btn" style="background:#2d89ef;color:#fff">Envoyer la réponse</button>
    <a class="btn" href="listReclamation.php">Annuler</a>
  </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
