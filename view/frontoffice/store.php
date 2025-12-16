<?php
session_start();

require_once __DIR__ . '/../../controller/StoreController.php';
require_once __DIR__ . '/../../controller/PartenaireController.php';

if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', realpath(dirname(dirname(__DIR__))));
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

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'Store';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($controllerName) {
    case 'Partenaire':
        $controller = new PartenaireController();
        $allowedActions = ['index', 'show', 'rate'];
        break;
    case 'Store':
    default:
        $controllerName = 'Store';
        $controller = new StoreController();
        $allowedActions = [
            'index',
            'show',
            'rateItem',
            'addToCart',
            'cart',
            'updateCart',
            'removeFromCart',
            'toggleLike',
            'addComment',
            'clearCart',
            'checkout',
            'wishlist'
        ];
        break;
}

if (!in_array($action, $allowedActions, true)) {
    $action = 'index';
}

if (method_exists($controller, $action)) {
    $controller->{$action}();
} else {
    http_response_code(404);
    echo "Action non supportée.";
}

