<?php
// Simple Router for Backoffice

// Define BASE_URL for correct asset loading
// Since we are in view/backoffice/router.php, root is 2 levels up
if (!defined('BASE_URL')) {
    define('BASE_URL', '../../');
}

// Include Database Config
require_once __DIR__ . '/../../db_config.php';

// Include Admin Controllers
require_once __DIR__ . '/../../controller/AdminStoreController.php';
require_once __DIR__ . '/../../controller/AdminPartenaireController.php';
require_once __DIR__ . '/../../controller/AdminOrderController.php';

// Get Controller and Action
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Dispatch
switch ($controllerName) {
    case 'AdminStore':
        $controller = new AdminStoreController();
        if ($action === 'index') {
            // Include view directly (Legacy support: view fetches data)
            include __DIR__ . '/store/items-list.php';
        } elseif (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("Action '$action' not found in AdminStoreController");
        }
        break;

    case 'AdminPartenaire':
        $controller = new AdminPartenaireController();
        if ($action === 'index') {
            // Include view directly
            include __DIR__ . '/partenaire/list.php';
        } elseif (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("Action '$action' not found in AdminPartenaireController");
        }
        break;

    case 'AdminOrder':
        $controller = new AdminOrderController();
        if ($action === 'index') {
            // Include view directly
            include __DIR__ . '/orders/orders-list.php';
        } elseif (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("Action '$action' not found in AdminOrderController");
        }
        break;

    case 'dashboard':
        header('Location: dashboard.php');
        break;

    default:
        // Try fallback to frontoffice if unknown, or die
        header('Location: ../../index.php');
        break;
}
