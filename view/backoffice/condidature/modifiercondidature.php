<?php
require_once __DIR__ . '/../../../controller/condidaturecontroller.php';

$condC = new condidaturecontroller();

$id = $_GET['id'] ?? null;
$c = $condC->getCandidatureById($id);

if (!$c) {
    echo "Candidature introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier Candidature</title>
    <!--<link rel="stylesheet" href="../assets/css/back.css">-->
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    
</head>

<body>

<div class="container mt-5 card-box p-4">

    <h2 class="text-white mb-4">Modifier Candidature</h2>

    <form action="../../../controller/condidaturecontroller.php?action=update" method="POST">

        <input type="hidden" name="id" value="<?= $c['id'] ?>">

        <label class="text-white">Volontaire :</label>
        <input class="form-control" type="text" value="<?= $c['pseudo_gaming'] ?>" disabled>

        <label class="text-white mt-3">Statut :</label>
        <select name="statut" class="form-control">
            <option value="en attente" <?= $c['statut']=="en attente"?"selected":"" ?>>En attente</option>
            <option value="acceptee" <?= $c['statut']=="acceptee"?"selected":"" ?>>Acceptée</option>
            <option value="refusee" <?= $c['statut']=="refusee"?"selected":"" ?>>Refusée</option>
        </select>
        <button class="btn btn-success mt-4" type="submit">Enregistrer</button>

    </form>
</div>

</body>
</html>
