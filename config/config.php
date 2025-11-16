<?php
class config {
    private static $db = NULL;

    public static function getConnexion() {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(
                    "mysql:host=localhost;dbname=projetweb;charset=utf8",
                    "root",
                    ""
                );
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die("Erreur : " . $e->getMessage());
            }
        }
        return self::$db;
    }
}
?>
