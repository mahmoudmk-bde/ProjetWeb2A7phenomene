<?php
require_once __DIR__ . '/../../db_config.php';

try {
    $pdo = config::getConnexion();
    $stmt = $pdo->query("SELECT DISTINCT statut FROM reclamation");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($statuses);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
