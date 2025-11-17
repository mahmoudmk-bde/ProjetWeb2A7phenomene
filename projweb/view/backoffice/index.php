<?php
/**
 * Point d'entrée principal de l'application
 * Gère le routage MVC
 */

// Définir le chemin racine
// Définir le chemin racine
define('ROOT_PATH', dirname(__DIR__, 2));
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = str_replace('/view/backoffice', '', $base_url);
$base_url = rtrim($base_url, '/') . '/';
define('BASE_URL', $base_url);

// Activer l'affichage des erreurs (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Charger la configuration
require_once ROOT_PATH . '/config/database.php';

// Charger les helpers
require_once __DIR__ . '/helpers.php';

// Charger les modèles
require_once ROOT_PATH . '/models/Partenaire.php';
require_once ROOT_PATH . '/models/StoreItem.php';

// Charger les contrôleurs
//require_once ROOT_PATH . '/controller/PartenaireController.php';
//require_once ROOT_PATH . '/controller/StoreController.php';
require_once ROOT_PATH . '/controller/AdminPartenaireController.php';
require_once ROOT_PATH . '/controller/AdminStoreController.php';

// Démarrer la session
session_start();

// Initialiser les variables de routage
$controller = isset($_GET['controller']) ? ucfirst(strtolower($_GET['controller'])) : 'AdminPartenaire';
$action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

// Mapper les contrôleurs
$controllerMap = [
    
    'Adminpartenaire' => 'AdminPartenaireController',
    'Adminstore' => 'AdminStoreController',
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
        // Action inexistante
        http_response_code(404);
        echo "<h1>404 - Action non trouvée</h1>";
        echo "Action '$action' introuvable dans le contrôleur '$controllerClass'";
    }
} else {
    // Contrôleur inexistant - afficher la page Store par défaut
    header("Location: ?controller=AdminPartenaire&action=index");
    exit;
}
?>
