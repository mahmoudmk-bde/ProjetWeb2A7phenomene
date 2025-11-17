<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/StoreItem.php';
require_once __DIR__ . '/../models/Partenaire.php';

class AdminStoreController {
    private $db;
    private $storeItem;
    private $partenaire;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->storeItem = new StoreItem($this->db);
        $this->partenaire = new Partenaire($this->db);
    }

    // Liste des items
    public function index() {
        $stmt = $this->storeItem->getAll();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../view/backoffice/store/items-list.php';
    }

    // Formulaire création
    public function create() {
        // Récupérer les partenaires actifs pour le select
        $stmt = $this->partenaire->getActifs();
        $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../view/backoffice/store/items-create.php';
    }

    // Traiter la création
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            // Validation
            if (empty($_POST['nom'])) {
                $errors[] = "Le nom du jeu est obligatoire";
            }
            if (empty($_POST['partenaire_id'])) {
                $errors[] = "Veuillez sélectionner un partenaire";
            }
            if (!isset($_POST['prix']) || $_POST['prix'] < 0) {
                $errors[] = "Le prix est invalide";
            }
            if (!isset($_POST['stock']) || $_POST['stock'] < 0) {
                $errors[] = "Le stock est invalide";
            }

            // Upload de l'image
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $this->storeItem->partenaire_id = $_POST['partenaire_id'];
                $this->storeItem->nom = $_POST['nom'];
                $this->storeItem->prix = $_POST['prix'];
                $this->storeItem->stock = $_POST['stock'];
                $this->storeItem->categorie = $_POST['categorie'];
                $this->storeItem->description = $_POST['description'] ?? '';
                $this->storeItem->plateforme = $_POST['plateforme'] ?? '';
                $this->storeItem->age_minimum = $_POST['age_minimum'] ?? 3;
                $this->storeItem->image = $imagePath;

                if ($this->storeItem->create()) {
                    $_SESSION['success'] = "Jeu ajouté au store avec succès";
                    header("Location: ?controller=AdminStore&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de l'ajout";
                }
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ?controller=AdminStore&action=create");
            exit;
        }
    }

    // Édition
    public function edit() {
        if (isset($_GET['id'])) {
            $this->storeItem->id = $_GET['id'];
            
            if ($this->storeItem->getById()) {
                // Récupérer les partenaires pour le select
                $stmt = $this->partenaire->getActifs();
                $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                include __DIR__ . '/../view/backoffice/store/items-edit.php';
            } else {
                $_SESSION['error'] = "Jeu introuvable";
                header("Location: ?controller=AdminStore&action=index");
                exit;
            }
        }
    }

    // Mise à jour
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $errors = [];
            
            if (empty($_POST['nom'])) {
                $errors[] = "Le nom est obligatoire";
            }

            // Récupérer l'ancienne image
            $this->storeItem->id = $_POST['id'];
            $this->storeItem->getById();
            $imagePath = $this->storeItem->image;

            // Upload nouvelle image si fournie
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                        @unlink(__DIR__ . '/../' . $imagePath);
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $this->storeItem->nom = $_POST['nom'];
                $this->storeItem->prix = $_POST['prix'];
                $this->storeItem->stock = $_POST['stock'];
                $this->storeItem->categorie = $_POST['categorie'];
                $this->storeItem->description = $_POST['description'] ?? '';
                $this->storeItem->plateforme = $_POST['plateforme'] ?? '';
                $this->storeItem->age_minimum = $_POST['age_minimum'] ?? 3;
                $this->storeItem->image = $imagePath;

                if ($this->storeItem->update()) {
                    $_SESSION['success'] = "Jeu mis à jour avec succès";
                    header("Location: ?controller=AdminStore&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la mise à jour";
                }
            }

            $_SESSION['errors'] = $errors;
            header("Location: ?controller=AdminStore&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

    // Suppression
    public function delete() {
        if (isset($_GET['id'])) {
            $this->storeItem->id = $_GET['id'];
            
            if ($this->storeItem->getById()) {
                $image = $this->storeItem->image;
                
                if ($this->storeItem->delete()) {
                    if ($image && file_exists(__DIR__ . '/../' . $image)) {
                        @unlink(__DIR__ . '/../' . $image);
                    }
                    $_SESSION['success'] = "Jeu supprimé avec succès";
                } else {
                    $_SESSION['error'] = "Erreur lors de la suppression";
                }
            }
            
            header("Location: ?controller=AdminStore&action=index");
            exit;
        }
    }

    // Helper upload image
    private function uploadImage($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
        }

        $uploadDir = __DIR__ . '/../view/frontoffice/assets/uploads/games/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('game_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'view/frontoffice/assets/uploads/games/' . $filename];
        }

        return ['success' => false, 'error' => 'Erreur lors du téléchargement'];
    }
}
?>