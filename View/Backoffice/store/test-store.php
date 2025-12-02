<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>TEST PAGE - If you see this, PHP is working</h1>";

try {
    require_once __DIR__ . '/../../../controller/AdminStoreController.php';
    echo "<p>Controller loaded successfully</p>";
    
    $storeC = new AdminStoreController();
    echo "<p>Controller instantiated successfully</p>";
    
    $items = $storeC->index();
    echo "<p>Items fetched: " . count($items) . " items found</p>";
    
    echo "<pre>";
    print_r($items);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>
