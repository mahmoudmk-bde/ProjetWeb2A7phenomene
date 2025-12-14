<?php
if (!class_exists('config')) {
    class config
    {
        private static $pdo = null;

        public static function getConnexion() {
            if (self::$pdo === null) {
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "projetweb3";
                $port = 3306;
                $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";
                
                try {
                    self::$pdo = new PDO($dsn, $username, $password);
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("Erreur de connexion : " . $e->getMessage());
                }
            }
            return self::$pdo;
        }
    }
}
