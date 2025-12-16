<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "projetweb3";
            $port = 3306; // Vérifiez si c'est bien le bon port

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
                die('Erreur de connexion: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>