<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Partenaire.php';

class AdminPartenaireController {
    private $db;
    private $partenaire;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->partenaire = new Partenaire($this->db);
    }

    // Afficher la liste des partenaires
    public function index() {
        $stmt = $this->partenaire->getAll();
        $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include __DIR__ . '/../view/backoffice/partenaire/list.php';
    }

    // Afficher le formulaire de création
    public function create() {
        include __DIR__ . '/../view/backoffice/partenaire/create.php';
    }

    // Traiter la création d'un partenaire
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $errors = [];
            
            if (empty($_POST['nom'])) {
                $errors[] = "Le nom est obligatoire";
            }
            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            if (empty($_POST['type'])) {
                $errors[] = "Le type est obligatoire";
            }

            // Upload du logo
            $logoPath = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $uploadResult = $this->uploadLogo($_FILES['logo']);
                if ($uploadResult['success']) {
                    $logoPath = $uploadResult['path'];
                } else {
                    $errors[] = $uploadResult['error'];
                }
            }

            if (empty($errors)) {
                // Remplir l'objet partenaire
                $this->partenaire->nom = $_POST['nom'];
                $this->partenaire->email = $_POST['email'];
                $this->partenaire->type = $_POST['type'];
                $this->partenaire->statut = $_POST['statut'] ?? 'en_attente';
                $this->partenaire->description = $_POST['description'] ?? '';
                $this->partenaire->telephone = $_POST['telephone'] ?? '';
                $this->partenaire->site_web = $_POST['site_web'] ?? '';
                $this->partenaire->logo = $logoPath;

                if ($this->partenaire->create()) {
                    $_SESSION['success'] = "Partenaire créé avec succès";
                    header("Location: ?controller=AdminPartenaire&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du partenaire";
                }
            }

            // Si erreurs, retourner au formulaire
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ?controller=AdminPartenaire&action=create");
            exit;
        }
    }

    // Afficher le formulaire d'édition
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

    // Mettre à jour un partenaire
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $errors = [];
            
            // Validation
            if (empty($_POST['nom'])) {
                $errors[] = "Le nom est obligatoire";
            }
            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }

            // Récupérer l'ancien logo
            $this->partenaire->id = $_POST['id'];
            $this->partenaire->getById();
            $logoPath = $this->partenaire->logo;

            // Upload du nouveau logo si fourni
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
                $this->partenaire->nom = $_POST['nom'];
                $this->partenaire->email = $_POST['email'];
                $this->partenaire->type = $_POST['type'];
                $this->partenaire->statut = $_POST['statut'];
                $this->partenaire->description = $_POST['description'] ?? '';
                $this->partenaire->telephone = $_POST['telephone'] ?? '';
                $this->partenaire->site_web = $_POST['site_web'] ?? '';
                $this->partenaire->logo = $logoPath;

                if ($this->partenaire->update()) {
                    $_SESSION['success'] = "Partenaire mis à jour avec succès";
                    header("Location: ?controller=AdminPartenaire&action=index");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la mise à jour";
                }
            }

            $_SESSION['errors'] = $errors;
            header("Location: ?controller=AdminPartenaire&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

    // Supprimer un partenaire
    public function delete() {
        if (isset($_GET['id'])) {
            $this->partenaire->id = $_GET['id'];
            
            // Récupérer le logo avant suppression
            if ($this->partenaire->getById()) {
                $logo = $this->partenaire->logo;
                
                if ($this->partenaire->delete()) {
                    // Supprimer le fichier logo
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

    // Fonction helper pour upload de logo
    private function uploadLogo($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Vérifier le type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.'];
        }

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 2MB)'];
        }

        // Créer le dossier si inexistant
        $uploadDir = __DIR__ . '/../view/frontoffice/assets/uploads/logos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'view/frontoffice/assets/uploads/logos/' . $filename];
        }

        return ['success' => false, 'error' => 'Erreur lors du téléchargement'];
    }
}
?>