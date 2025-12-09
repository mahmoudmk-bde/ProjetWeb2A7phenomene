<?php

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/Partenaire.php';

class AdminPartenaireController {
    private $db;
    private $partenaire;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
        $this->partenaire = new Partenaire($this->db);
    }

    // Affich liste parten
    public function index() {
        $stmt = $this->partenaire->getAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Afficher formulaire creation
    public function create() {
        include __DIR__ . '/../view/backoffice/partenaire/create.php';
    }

    // creation partenaire
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // validation COMPLÈTE avec tous les champs obligatoires
            $errors = [];
            
            // Validation de tous les champs obligatoires
            if (empty(trim($_POST['nom']))) {
                $errors[] = "Le nom est obligatoire";
            }
            if (empty(trim($_POST['email']))) {
                $errors[] = "L'email est obligatoire";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            if (empty($_POST['type'])) {
                $errors[] = "Le type est obligatoire";
            }
            if (empty(trim($_POST['telephone']))) {
                $errors[] = "Le numéro de téléphone est obligatoire";
            }
            if (empty(trim($_POST['site_web']))) {
                $errors[] = "Le site web est obligatoire";
            }
            if (empty($_POST['statut'])) {
                $errors[] = "Le statut est obligatoire";
            }

            // Validation du logo (obligatoire)
            $logoPath = null;
            if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== 0) {
                $errors[] = "Le logo est obligatoire";
            } else {
                $uploadResult = $this->uploadLogo($_FILES['logo']);
                if ($uploadResult['success']) {
                    $logoPath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            // Si pas d'erreurs, procéder à la création
            if (empty($errors)) {
                // remplissage
                $this->partenaire->nom = trim($_POST['nom']);
                $this->partenaire->email = trim($_POST['email']);
                $this->partenaire->type = $_POST['type'];
                $this->partenaire->statut = $_POST['statut'];
                $this->partenaire->description = $_POST['description'] ?? '';
                $this->partenaire->telephone = trim($_POST['telephone']);
                $this->partenaire->site_web = trim($_POST['site_web']);
                $this->partenaire->logo = $logoPath;

                if ($this->partenaire->create()) {
                    $_SESSION['success'] = "Partenaire créé avec succès";
                    header("Location: ?controller=AdminPartenaire&action=index");
                    exit;
                } else {
                    // Message d'erreur PLUS SPÉCIFIQUE
                    $errors[] = "Erreur lors de la création dans la base de données. Vérifiez que l'email n'existe pas déjà.";
                }
            }

            // Si erreurs, les afficher
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ?controller=AdminPartenaire&action=create");
            exit;
        }
    }

    // Affich formulaire edit
    public function edit() {
        if (isset($_GET['id'])) {
            $this->partenaire->id = $_GET['id'];
            
            if ($this->partenaire->getById()) {
                include __DIR__ . '/../view/backoffice/partenaire/edit.php';
            } else {
                $_SESSION['error'] = "Partenaire introuvable";
                header("Location: ?controller=AdminPartenaire&action=index");
                exit;
            }
        }
    }

    // mise a jour partenaire
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $errors = [];
            
            // Validation COMPLÈTE
            if (empty(trim($_POST['nom']))) {
                $errors[] = "Le nom est obligatoire";
            }
            if (empty(trim($_POST['email']))) {
                $errors[] = "L'email est obligatoire";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            if (empty($_POST['type'])) {
                $errors[] = "Le type est obligatoire";
            }
            if (empty(trim($_POST['telephone']))) {
                $errors[] = "Le numéro de téléphone est obligatoire";
            }
            if (empty(trim($_POST['site_web']))) {
                $errors[] = "Le site web est obligatoire";
            }
            if (empty($_POST['statut'])) {
                $errors[] = "Le statut est obligatoire";
            }

            //ancien logo
            $this->partenaire->id = $_POST['id'];
            $this->partenaire->getById();
            $logoPath = $this->partenaire->logo;

            // new logo (optionnel en modification)
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $uploadResult = $this->uploadLogo($_FILES['logo']);
                if ($uploadResult['success']) {
                    // Supprimer l'ancien logo
                    if ($logoPath && file_exists(__DIR__ . '/../' . $logoPath)) {
                        @unlink(__DIR__ . '/../' . $logoPath);
                    }
                    $logoPath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                $this->partenaire->nom = trim($_POST['nom']);
                $this->partenaire->email = trim($_POST['email']);
                $this->partenaire->type = $_POST['type'];
                $this->partenaire->statut = $_POST['statut'];
                $this->partenaire->description = $_POST['description'] ?? '';
                $this->partenaire->telephone = trim($_POST['telephone']);
                $this->partenaire->site_web = trim($_POST['site_web']);
                $this->partenaire->logo = $logoPath;

                if ($this->partenaire->update()) {
                    $_SESSION['success'] = "Partenaire mis à jour avec succès";
                    header("Location: ?controller=AdminPartenaire&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la mise à jour dans la base de données";
                }
            }

            $_SESSION['errors'] = $errors;
            header("Location: ?controller=AdminPartenaire&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

    // Supp partenaire
    public function delete() {
        if (isset($_GET['id'])) {
            $this->partenaire->id = $_GET['id'];
            
            // supp logo
            if ($this->partenaire->getById()) {
                $logo = $this->partenaire->logo;
                
                if ($this->partenaire->delete()) {
                    
                    if ($logo && file_exists(__DIR__ . '/../' . $logo)) {
                        @unlink(__DIR__ . '/../' . $logo);
                    }
                    
                    $_SESSION['success'] = "Partenaire supprimé avec succès";
                } else {
                    $_SESSION['error'] = "Erreur lors de la suppression";
                }
            }
            
            header("Location: ?controller=AdminPartenaire&action=index");
            exit;
        }
    }

    // upload logo
    private function uploadLogo($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.'];
        }

        // taille
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 2MB)'];
        }

        // dossier inexistant
        $uploadDir = __DIR__ . '/../view/frontoffice/assets/uploads/logos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // deplacer fichier
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'view/frontoffice/assets/uploads/logos/' . $filename];
        }

        return ['success' => false, 'error' => 'Erreur lors du téléchargement'];
    }
}
?>