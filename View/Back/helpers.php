<?php
/**
 * Back helpers copied from partenaire eya template
 */

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

function getTypeColor($type) {
    $colors = [
        'sponsor' => 'primary',
        'testeur' => 'info',
        'vendeur' => 'success'
    ];
    return $colors[$type] ?? 'secondary';
}

function getTypeLabel($type) {
    $labels = [
        'sponsor' => 'Sponsor',
        'testeur' => 'Testeur',
        'vendeur' => 'Vendeur'
    ];
    return $labels[$type] ?? 'Partenaire';
}

function getStatusLabel($status) {
    $labels = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'en_attente' => 'En attente'
    ];
    return $labels[$status] ?? 'Inconnu';
}

function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

?>
