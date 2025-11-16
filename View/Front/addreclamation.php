<?php
require_once "../../Controller/ReclamationController.php";

if ($_SERVER['REQUEST_METHOD']==="POST") {
    $rec = new Reclamation($_POST['sujet'], $_POST['description'], $_POST['email']);
    $ctrl = new ReclamationController();
    $ctrl->addReclamation($rec);
    echo "<p>Réclamation ajoutée avec succès !</p>";
}
?>

<!DOCTYPE html>
<html>
<head
    meta charset="UTF-8"><title>Ajouter Réclamation</title>
    
</head>
<body>
<h2>Ajouter une réclamation</h2>
<form method="POST" action="">
    <label>Sujet:</label><br>
    <input type="text" name="sujet" required><br><br>
    <label>Description:</label><br>
    <textarea name="description" rows="5" cols="50" required></textarea><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Envoyer</button>
</form>
</body>
</html>
