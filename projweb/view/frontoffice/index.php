<?php
/**
 * Point d'entrée FRONTOFFICE
 * Gère le routage MVC pour le site public
 */

// Définir le chemin racine
define('ROOT_PATH', dirname(__DIR__, 2));
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = str_replace('/view/frontoffice', '', $base_url);
$base_url = rtrim($base_url, '/') . '/';
define('BASE_URL', $base_url);
// Activer l'affichage des erreurs (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Charger la configuration
require_once ROOT_PATH . '/config/database.php';

// Charger les helpers
//require_once ROOT_PATH . '/helpers.php';

// Charger les modèles
require_once ROOT_PATH . '/models/Partenaire.php';
require_once ROOT_PATH . '/models/StoreItem.php';

// Charger les contrôleurs FRONTOFFICE uniquement
require_once ROOT_PATH . '/controller/PartenaireController.php';
require_once ROOT_PATH . '/controller/StoreController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables de routage
$controller = isset($_GET['controller']) ? ucfirst(strtolower($_GET['controller'])) : 'Store';
$action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

// Mapper les contrôleurs FRONTOFFICE uniquement
$controllerMap = [
    'Partenaire' => 'PartenaireController',
    'Store' => 'StoreController',
];

// Vérifier si le contrôleur existe
if (isset($controllerMap[$controller])) {
    $controllerClass = $controllerMap[$controller];
    
    // Créer une instance du contrôleur
    $controllerInstance = new $controllerClass();
    
    // Vérifier si la méthode existe
    if (method_exists($controllerInstance, $action)) {
        // Appeler la méthode
        $controllerInstance->$action();
    } else {
        // Action inexistante - rediriger vers le store
        header("Location: ?controller=Store&action=index");
        exit;
    }
} else {
    // Contrôleur inexistant - afficher la page Store par défaut
    $controllerInstance = new StoreController();
    $controllerInstance->index();
}
?>