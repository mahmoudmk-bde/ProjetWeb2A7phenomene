<?php
session_start();

// Pages accessibles sans connexion
$public_pages = ['index.php', 'connexion.php', 'inscription.php', 'missionlist.php', 'missiondetails.php', 'contact.php', 'mdp.php'];

// Pages nécessitant une connexion
$private_pages = ['profile.php', 'settings.php', 'securite.php', 'addcondidature.php'];

$current_page = basename($_SERVER['PHP_SELF']);

// Rediriger vers la connexion si page privée et non connecté
if (in_array($current_page, $private_pages) && !isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Rediriger vers l'accueil si déjà connecté et sur une page de connexion
if (in_array($current_page, ['connexion.php', 'inscription.php']) && isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>