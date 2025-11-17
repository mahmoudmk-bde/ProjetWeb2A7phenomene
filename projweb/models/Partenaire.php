<?php
class Partenaire {
    private $conn;
    private $table = "partenaires";
    
    public $id;
    public $nom;
    public $logo;
    public $type;
    public $statut;
    public $description;
    public $email;
    public $telephone;
    public $site_web;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // MÉTHODE CREATE CORRIGÉE
    public function create() {
        // CORRECTION : Requête SQL complète
        $query = "INSERT INTO " . $this->table . " 
                  SET nom = :nom, 
                      logo = :logo, 
                      type = :type, 
                      statut = :statut, 
                      description = :description, 
                      email = :email, 
                      telephone = :telephone, 
                      site_web = :site_web,
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description ?? ''));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // CORRECTION : Binding complet
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":logo", $this->logo);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":site_web", $this->site_web);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur Partenaire::create: " . $e->getMessage());
            return false;
        }
    }
    
    // MÉTHODE UPDATE CORRIGÉE
    public function update() {
        // CORRECTION : Requête UPDATE complète
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, 
                      logo = :logo, 
                      type = :type, 
                      statut = :statut, 
                      description = :description, 
                      email = :email, 
                      telephone = :telephone, 
                      site_web = :site_web,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description ?? ''));
        
        // CORRECTION : Binding complet
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":logo", $this->logo);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":site_web", $this->site_web);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur Partenaire::update: " . $e->getMessage());
            return false;
        }
    }
    
    // Les autres méthodes sont correctes
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getActifs() {
        $query = "SELECT * FROM " . $this->table . " WHERE statut = 'actif' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getById() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->logo = $row['logo'];
            $this->type = $row['type'];
            $this->statut = $row['statut'];
            $this->description = $row['description'];
            $this->email = $row['email'];
            $this->telephone = $row['telephone'];
            $this->site_web = $row['site_web'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }
    
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur Partenaire::delete: " . $e->getMessage());
            return false;
        }
    }
}
?>