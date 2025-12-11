<?php
require_once __DIR__ . '/../../db_config.php';

try {
    $pdo = config::getConnexion();
    $stmt = $pdo->query("SHOW COLUMNS FROM reclamation");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
