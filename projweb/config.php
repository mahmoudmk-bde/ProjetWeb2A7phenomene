<?php
class Database {
    // PROPRIÉTÉS - Informations de connexion
    private $host = "localhost";      // Serveur MySQL
    private $db_name = "projetweb";   // Nom de la base de données
    private $username = "root";        // Utilisateur MySQL
    private $password = "";            // Mot de passe MySQL
    private $conn;                     // Objet de connexion PDO
    
    // MÉTHODE - Créer et retourner la connexion
    public function getConnection() {
        // Si pas encore connecté
        $this->conn = null;
        
        try {
            // Créer une nouvelle connexion PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Configuration PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>