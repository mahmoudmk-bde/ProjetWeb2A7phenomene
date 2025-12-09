<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php');
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

function isOrganization() {
    return getUserType() === 'organisation';
}

function isVolunteer() {
    return getUserType() === 'volontaire';
}
?>