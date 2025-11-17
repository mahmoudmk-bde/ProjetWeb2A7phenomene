<?php
/**
 * ENGAGE - Gamification Module
 * Helper Functions
 */

/**
 * Format une date en format français
 */
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Format un prix en devise EUR
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Retourne la couleur de badge pour un type
 */
function getTypeColor($type) {
    $colors = [
        'sponsor' => 'primary',
        'testeur' => 'info',
        'vendeur' => 'success'
    ];
    return $colors[$type] ?? 'secondary';
}

/**
 * Retourne le label pour un type
 */
function getTypeLabel($type) {
    $labels = [
        'sponsor' => 'Sponsor',
        'testeur' => 'Testeur',
        'vendeur' => 'Vendeur'
    ];
    return $labels[$type] ?? 'Partenaire';
}

/**
 * Retourne le label pour un statut
 */
function getStatusLabel($status) {
    $labels = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'en_attente' => 'En attente'
    ];
    return $labels[$status] ?? 'Inconnu';
}

/**
 * Truncate un texte
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Escape HTML
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si un fichier image est valide
 */
function isValidImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    return in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize;
}

/**
 * Génère un nom de fichier unique
 */
function generateUniqueFileName($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Retourne le chemin d'upload du répertoire
 */
function getUploadDir($type = 'images') {
    $dirs = [
        'images' => '/assets/uploads/images/',
        'logos' => '/assets/uploads/logos/',
        'games' => '/assets/uploads/games/'
    ];
    return $dirs[$type] ?? $dirs['images'];
}

/**
 * Log des activités
 */
function logActivity($action, $details = '') {
    $logFile = __DIR__ . '/../../logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] Action: $action - Details: $details\n";
    error_log($logMessage, 3, $logFile);
}

/**
 * Retourne les catégories disponibles
 */
function getCategories() {
    return [
        'action' => 'Action',
        'aventure' => 'Aventure',
        'sport' => 'Sport',
        'strategie' => 'Stratégie',
        'simulation' => 'Simulation',
        'rpg' => 'RPG'
    ];
}

/**
 * Retourne les types de partenaires
 */
function getPartnerTypes() {
    return [
        'sponsor' => 'Sponsor',
        'testeur' => 'Testeur',
        'vendeur' => 'Vendeur'
    ];
}

/**
 * Retourne les statuts disponibles
 */
function getStatusOptions() {
    return [
        'en_attente' => 'En attente',
        'actif' => 'Actif',
        'inactif' => 'Inactif'
    ];
}

/**
 * Valide une URL
 */
function isValidURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Valide un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un téléphone français
 */
function isValidPhone($phone) {
    $pattern = '/^(?:(?:\+|00)33|0)[1-9](?:[0-9]{8})$/';
    return preg_match($pattern, str_replace([' ', '.', '-'], '', $phone)) === 1;
}

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect vers une URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Retourne l'indicateur de stock
 */
function getStockStatus($stock) {
    if ($stock <= 0) {
        return ['status' => 'out', 'label' => 'Rupture de stock', 'color' => 'danger'];
    } elseif ($stock <= 5) {
        return ['status' => 'low', 'label' => 'Stock faible', 'color' => 'warning'];
    } else {
        return ['status' => 'good', 'label' => 'En stock', 'color' => 'success'];
    }
}
?>
