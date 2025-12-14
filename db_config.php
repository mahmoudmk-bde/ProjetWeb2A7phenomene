<?php
if (!class_exists('config')) {
    class config
    {
<<<<<<< HEAD
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "projetweb3";
            $port = 3306; // Vérifiez si c'est bien le bon port
            
=======
        public static function getConnexion()
        {
            $host = "localhost";
            $db   = "projetweb3"; 
            $user = "root";
            $pass = "";

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8";

>>>>>>> 435a8ac689491ba15e90a3bbe2ddb576d6e4b42d
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
