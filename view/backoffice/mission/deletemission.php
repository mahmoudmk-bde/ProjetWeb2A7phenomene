<?php
require_once __DIR__ . '/../../../controller/missioncontroller.php';

if (isset($_GET['id'])) {
    $missionC = new missioncontroller();
    $missionC->deletemission((int) $_GET['id']);
}

header('Location: missionliste.php');
exit;
