<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', realpath(dirname(dirname(dirname(__FILE__)))));
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT'])
        ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']))
        : '';

    $basePath = '';
    if ($documentRoot && strpos($projectRoot, $documentRoot) === 0) {
        $basePath = substr($projectRoot, strlen($documentRoot));
    }
    $basePath = '/' . trim($basePath, '/');
    if ($basePath !== '/') {
        $basePath .= '/';
    }
    define('BASE_URL', $basePath === '//' ? '/' : $basePath);
}

// Include Controllers
require_once __DIR__ . '/../../controller/AdminPartenaireController.php';
require_once __DIR__ . '/../../controller/AdminStoreController.php';
require_once __DIR__ . '/../../controller/AdminOrderController.php';

// Get Controller and Action
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'Dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Dispatch
switch ($controllerName) {
    case 'AdminPartenaire':
        $controller = new AdminPartenaireController();
        break;
    case 'AdminStore':
        $controller = new AdminStoreController();
        break;
    case 'AdminOrder':
        $controller = new AdminOrderController();
        break;
    default:
        // Default to dashboard or error
        header('Location: dashboard.php');
        exit;
}

if (method_exists($controller, $action)) {
    $controller->{$action}();
} else {
    // Handle error: Action not found
    die("Action '$action' not found in controller '$controllerName'");
}
