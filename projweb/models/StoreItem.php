<?php
class StoreItem {
    private $conn;
    private $table = "store_items";

    // Propriétés
    public $id;
    public $partenaire_id;
    public $nom;
    public $prix;
    public $stock;
    public $categorie;
    public $image;
    public $description;
    public $plateforme;
    public $age_minimum;
    public $created_at;
    public $partenaire_nom; // Nom du partenaire (via jointure)

    // Constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET partenaire_id = :partenaire_id,
                    nom = :nom,
                    prix = :prix,
                    stock = :stock,
                    categorie = :categorie,
                    image = :image,
                    description = :description,
                    plateforme = :plateforme,
                    age_minimum = :age_minimum";

        $stmt = $this->conn->prepare($query);

        // Nettoyer
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie = htmlspecialchars(strip_tags($this->categorie));

        // Binding
        $stmt->bindParam(":partenaire_id", $this->partenaire_id);
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prix", $this->prix);
        $stmt->bindParam(":stock", $this->stock);
        $stmt->bindParam(":categorie", $this->categorie);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":plateforme", $this->plateforme);
        $stmt->bindParam(":age_minimum", $this->age_minimum);

        try {
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur StoreItem::create: " . $e->getMessage());
        return false;
    }
}

    // READ - Tous les items
    public function getAll() {
        $query = "SELECT s.*, p.nom as partenaire_nom 
                  FROM " . $this->table . " s
                  LEFT JOIN partenaires p ON s.partenaire_id = p.id
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ - Items par partenaire
    public function getByPartenaire($partenaire_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE partenaire_id = :partenaire_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":partenaire_id", $partenaire_id);
        $stmt->execute();
        return $stmt;
    }

    // READ - Item par ID
    public function getById() {
        $query = "SELECT s.*, p.nom as partenaire_nom, p.logo as partenaire_logo
                  FROM " . $this->table . " s
                  LEFT JOIN partenaires p ON s.partenaire_id = p.id
                  WHERE s.id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->partenaire_id = $row['partenaire_id'];
            $this->nom = $row['nom'];
            $this->prix = $row['prix'];
            $this->stock = $row['stock'];
            $this->categorie = $row['categorie'];
            $this->image = $row['image'];
            $this->description = $row['description'];
            $this->plateforme = $row['plateforme'];
            $this->age_minimum = $row['age_minimum'];
            $this->created_at = $row['created_at'];
            $this->partenaire_nom = $row['partenaire_nom'] ?? null;
            return true;
        }
        return false;
    }

    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET nom = :nom,
                    prix = :prix,
                    stock = :stock,
                    categorie = :categorie,
                    image = :image,
                    description = :description,
                    plateforme = :plateforme,
                    age_minimum = :age_minimum
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer
        $this->nom = htmlspecialchars(strip_tags($this->nom));

        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prix", $this->prix);
        $stmt->bindParam(":stock", $this->stock);
        $stmt->bindParam(":categorie", $this->categorie);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":plateforme", $this->plateforme);
        $stmt->bindParam(":age_minimum", $this->age_minimum);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // UPDATE - Mettre à jour le stock
    public function updateStock($quantite) {
        $query = "UPDATE " . $this->table . "
                SET stock = stock - :quantite
                WHERE id = :id AND stock >= :quantite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":quantite", $quantite);
        
        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // DELETE
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>