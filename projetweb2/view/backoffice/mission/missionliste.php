<?php
require_once __DIR__ . '/../../../controller/missioncontroller.php';
$missionC = new missioncontroller();
$missions = $missionC->missionliste();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des missions</title>

    <link rel="stylesheet" href="../assets/css/back.css">
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../frontoffice/css/all.css">
</head>

<body>



<div class="container mt-4">

    <h2 class="mb-4">Liste des missions</h2>

    <a href="addmission.php" class="btn btn-success mb-3">+ Ajouter une mission</a>

<div class="mission-grid">

<?php foreach ($missions as $m): ?>
    <div class="mission-card">
        
        <div class="mission-title">
            <?= htmlspecialchars($m['titre']) ?>
        </div>

        <div class="mission-info">
            <i class="fas fa-gamepad"></i> Jeu : <b><?= htmlspecialchars($m['jeu']) ?></b>
        </div>

        <div class="mission-info">
            <i class="fas fa-tags"></i> Thème : <?= htmlspecialchars($m['theme']) ?>
        </div>

        <div class="mission-info">
            <i class="fas fa-fire"></i> Difficulté : <?= htmlspecialchars($m['niveau_difficulte']) ?>
        </div>

        <div class="mission-actions">
            <a href="../condidature/listecondidature.php?mission_id=<?= $m['id'] ?>" 
               class="btn-small btn-view">
                Voir candidatures
            </a>

            <a href="deletemission.php?id=<?= $m['id'] ?>"
               onclick="return confirm('Supprimer cette mission ?')"
               class="btn-small btn-delete">
               Supprimer
            </a>
        </div>

    </div>
<?php endforeach; ?>

</div>


</div>
</div>

<script src="../assets/js/back.js"></script>

</body>
</html>
