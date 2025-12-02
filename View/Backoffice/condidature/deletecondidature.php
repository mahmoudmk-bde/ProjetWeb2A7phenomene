<?php
require_once __DIR__ . '/../../../controller/condidaturecontroller.php';

if (isset($_GET['id'])) {
    $condC = new condidaturecontroller();
    $condC->deleteCondidature((int) $_GET['id']);
}

header('Location: listecondidature.php');
exit;
