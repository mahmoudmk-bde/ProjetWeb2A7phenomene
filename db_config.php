<?php
if (!class_exists('config')) {
    class config
    {
        public static function getConnexion()
        {
            $host = "localhost";
            $db   = "projetweb3"; 
            $user = "root";
            $pass = "";

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8";

            try {
                $pdo = new PDO($dsn, $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
    }
}

// Helper function to sanitize input
if (!function_exists('secure_data')) {
    function secure_data($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
