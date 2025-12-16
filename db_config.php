<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        public static function getConnexion()
        {
            $host = "localhost";
            $db   = "projetweb3"; 
            $user = "root";
            $pass = "";

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8";

            try {
                // Ajout du port dans la chaîne de connexion
                self::$pdo = new PDO(
                    "mysql:host=$servername;port=$port;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

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
