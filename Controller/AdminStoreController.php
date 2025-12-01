<?php

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/StoreItem.php';
require_once __DIR__ . '/../model/Partenaire.php';

class AdminStoreController {
    private $db;
    private $storeItem;
    private $partenaire;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
        $this->storeItem = new StoreItem($this->db);
        $this->partenaire = new Partenaire($this->db);
    }

    // liste items
    public function index() {
        $stmt = $this->storeItem->getAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Formulaire creation
    public function create() {
        // recup partenaire actif
        $stmt = $this->partenaire->getActifs();
        $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../view/backoffice/store/items-create.php';
    }

    // creation
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // validation COMPLÈTE avec tous les champs obligatoires SAUF description
            $errors = [];
            
            // Validation de tous les champs obligatoires
            if (empty(trim($_POST['nom']))) {
                $errors[] = "Le nom du jeu est obligatoire";
            }
            if (empty($_POST['partenaire_id'])) {
                $errors[] = "Veuillez sélectionner un partenaire";
            }
            if (empty(trim($_POST['prix'])) && $_POST['prix'] !== '0') {
                $errors[] = "Le prix est obligatoire";
            } elseif (!is_numeric($_POST['prix']) || $_POST['prix'] < 0) {
                $errors[] = "Le prix est invalide";
            }
            // CORRECTION : Stock peut être 0
            if (!isset($_POST['stock']) || $_POST['stock'] === '') {
                $errors[] = "Le stock est obligatoire";
            } elseif (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
                $errors[] = "Le stock est invalide";
            }
            if (empty($_POST['categorie'])) {
                $errors[] = "La catégorie est obligatoire";
            }
            if (empty(trim($_POST['plateforme']))) {
                $errors[] = "La plateforme est obligatoire";
            }
            if (empty($_POST['age_minimum'])) {
                $errors[] = "L'âge minimum est obligatoire";
            }

            // image (obligatoire en création)
            $imagePath = null;
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
                $errors[] = "L'image du jeu est obligatoire";
            } else {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            // Si pas d'erreurs, procéder à la création
            if (empty($errors)) {
                $this->storeItem->partenaire_id = $_POST['partenaire_id'];
                $this->storeItem->nom = trim($_POST['nom']);
                $this->storeItem->prix = $_POST['prix'];
                $this->storeItem->stock = $_POST['stock'];
                $this->storeItem->categorie = $_POST['categorie'];
                $this->storeItem->description = $_POST['description'] ?? ''; // SEUL champ optionnel
                $this->storeItem->plateforme = trim($_POST['plateforme']);
                $this->storeItem->age_minimum = $_POST['age_minimum'];
                $this->storeItem->image = $imagePath;

                if ($this->storeItem->create()) {
                    $_SESSION['success'] = "Jeu ajouté au store avec succès";
                    header("Location: ?controller=AdminStore&action=index");
                    exit;
                } else {
                    // Message d'erreur PLUS SPÉCIFIQUE
                    $errors[] = "Erreur lors de l'ajout dans la base de données. Vérifiez les données.";
                }
            }

            // Si erreurs, les afficher
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ?controller=AdminStore&action=create");
            exit;
        }
    }

    // Mise a jour
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            // validation COMPLÈTE
            $errors = [];
            
            // Validation de tous les champs obligatoires SAUF description
            if (empty(trim($_POST['nom']))) {
                $errors[] = "Le nom du jeu est obligatoire";
            }
            if (empty($_POST['partenaire_id'])) {
                $errors[] = "Veuillez sélectionner un partenaire";
            }
            if (empty(trim($_POST['prix'])) && $_POST['prix'] !== '0') {
                $errors[] = "Le prix est obligatoire";
            } elseif (!is_numeric($_POST['prix']) || $_POST['prix'] < 0) {
                $errors[] = "Le prix est invalide";
            }
            // CORRECTION : Stock peut être 0
            if (!isset($_POST['stock']) || $_POST['stock'] === '') {
                $errors[] = "Le stock est obligatoire";
            } elseif (!is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
                $errors[] = "Le stock est invalide";
            }
            if (empty($_POST['categorie'])) {
                $errors[] = "La catégorie est obligatoire";
            }
            if (empty(trim($_POST['plateforme']))) {
                $errors[] = "La plateforme est obligatoire";
            }
            if (empty($_POST['age_minimum'])) {
                $errors[] = "L'âge minimum est obligatoire";
            }

            // recup image actuelle
            $this->storeItem->id = $_POST['id'];
            $this->storeItem->getById();
            $imagePath = $this->storeItem->image;

            // telecharger image (optionnel en modification)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    // Supprimer l'ancienne image si elle existe
                    if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                        @unlink(__DIR__ . '/../' . $imagePath);
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $this->storeItem->nom = trim($_POST['nom']);
                $this->storeItem->partenaire_id = $_POST['partenaire_id'];
                $this->storeItem->prix = $_POST['prix'];
                $this->storeItem->stock = $_POST['stock'];
                $this->storeItem->categorie = $_POST['categorie'];
                $this->storeItem->description = $_POST['description'] ?? ''; // SEUL champ optionnel
                $this->storeItem->plateforme = trim($_POST['plateforme']);
                $this->storeItem->age_minimum = $_POST['age_minimum'];
                $this->storeItem->image = $imagePath;

                if ($this->storeItem->update()) {
                    $_SESSION['success'] = "Jeu mis à jour avec succès";
                    header("Location: ?controller=AdminStore&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la mise à jour dans la base de données";
                }
            }

            $_SESSION['errors'] = $errors;
            header("Location: ?controller=AdminStore&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

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

    private function uploadImage($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 5MB)'];
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