<?php
if (!class_exists('config')) {
    class config
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "projetweb3";
            $port = 3306; // Vérifiez si c'est bien le bon port
            
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
