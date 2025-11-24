<?php
class config
{
    public static function getConnexion()
    {
        $host = "localhost";
        $db   = "projetweb"; 
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
