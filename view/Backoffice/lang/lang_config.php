<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_POST['lang'])) {
    $_SESSION['bo_lang'] = $_POST['lang'];
}

$lang = $_SESSION['bo_lang'] ?? 'fr';
$allowed_langs = ['fr', 'en', 'ar'];

if (!in_array($lang, $allowed_langs)) {
    $lang = 'fr';
}

$translations = require __DIR__ . "/$lang.php";

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

function get_current_lang() {
    global $lang;
    return $lang;
}

function get_dir() {
    global $lang;
    return $lang === 'ar' ? 'rtl' : 'ltr';
}
?>
