<?php
// Usage: include this partial in admin list/detail views
// Expected variables:
// - $reclamation (assoc array with 'id') OR $reclamationId
// - controller available
require_once __DIR__ . '/../../../controller/ReclamationController.php';
$ctrl = new ReclamationController();
$rid = isset($reclamation['id']) ? (int)$reclamation['id'] : (int)($reclamationId ?? 0);
$tags = $rid ? $ctrl->getTagsByReclamation($rid) : [];
?>
<link rel="stylesheet" href="../assets/css/tags.css">
<div class="tags-wrap">
<?php foreach($tags as $tag): ?>
  <span class="tag-pill" data-tag="<?= htmlspecialchars($tag) ?>">
    <span class="dot"></span>#<?= htmlspecialchars($tag) ?>
  </span>
<?php endforeach; ?>
<?php if(empty($tags)): ?>
  <span style="color:#b0b3c1;">Aucun tag</span>
<?php endif; ?>
</div>
<script src="../assets/js/tags.js"></script>
