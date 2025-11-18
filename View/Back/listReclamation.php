<?php
// View/Back/listReclamation.php
require_once __DIR__ . '/../../Controller/ReclamationController.php';
require_once __DIR__ . '/../../Controller/ResponseController.php';

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$list = $recCtrl->listReclamations();

// Set page variables
$pageTitle = 'Réclamations - Backoffice';
$activePage = 'reclamations';

// Build content using output buffering
ob_start();
?>
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0"><i class="fas fa-exclamation-circle"></i> Liste des Réclamations</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sujet</th>
            <th>Email</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Réponses</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($list)): ?>
          <?php foreach ($list as $rec): ?>
            <tr>
              <td><?= htmlspecialchars($rec['id']) ?></td>
              <td><?= htmlspecialchars($rec['sujet']) ?></td>
              <td><?= htmlspecialchars($rec['email']) ?></td>
              <td><?= htmlspecialchars($rec['date_creation'] ?? '') ?></td>
              <td>
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
              </td>
              <td>
                <?php
                  $responses = $respCtrl->getResponses($rec['id']);
                  if ($responses) {
                    echo '<span class="badge badge-success">' . count($responses) . '</span>';
                  } else {
                    echo '<span class="badge badge-info">0</span>';
                  }
                ?>
              </td>
              <td>
                <a class="btn btn-primary btn-sm" href="response.php?id=<?= $rec['id'] ?>" style="padding: 5px 10px; font-size: 12px;"><i class="fas fa-reply"></i> Répondre</a>
                <a class="btn btn-sm" style="background:#6c757d; color:#fff; padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 25px;" href="details.php?id=<?= $rec['id'] ?>"><i class="fas fa-eye"></i> Voir</a>
                <a class="btn btn-sm" style="background:#dc3545; color:#fff; padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 25px;" href="delete.php?id=<?= $rec['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')"><i class="fas fa-trash"></i> Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center"><em>Aucune réclamation trouvée.</em></td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
