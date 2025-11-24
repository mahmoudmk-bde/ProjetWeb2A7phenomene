<?php
require_once __DIR__ . '/../../../controller/condidaturecontroller.php';

$condC = new condidaturecontroller();
$liste = $condC->getAllCondidatures();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Toutes les candidatures</title>

    <link rel="stylesheet" href="../assets/css/back.css">
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../frontoffice/css/all.css">
</head>

<body>


<div class="container mt-4">

<h2 class="mb-4">Toutes les candidatures</h2>

<div class="table-container">
<table class="table-modern">

    <thead>
        <tr>
            <th>ID</th>
            <th>Mission</th>
            <th>Pseudo gaming</th>
            <th>Email</th>
            <th>Niveau</th>
            <th>Disponibilités</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($liste as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['mission_titre']) ?></td>
            <td><?= htmlspecialchars($c['pseudo_gaming']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['niveau_experience']) ?></td>
            <td><?= htmlspecialchars($c['disponibilites']) ?></td>

            <td>
                <span class="badge <?= $c['statut'] ?>">
                    <?= $c['statut'] ?>
                </span>
            </td>

            <td>
                <a class="btn-delete"
                   href="deletecondidature.php?id=<?= $c['id'] ?>"
                   onclick="return confirm('Supprimer cette candidature ?')">
                   Supprimer
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>

</div>
</div>

<script src="../assets/js/back.js"></script>

</body>
</html>
