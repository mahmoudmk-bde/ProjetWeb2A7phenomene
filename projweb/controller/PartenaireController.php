<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Partenaire.php';
require_once __DIR__ . '/../models/StoreItem.php';

class PartenaireController {
    private $db;
    private $partenaire;
    private $storeItem;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->partenaire = new Partenaire($this->db);
        $this->storeItem = new StoreItem($this->db);
    }

    // Liste des partenaires
    public function index() {
        $stmt = $this->partenaire->getActifs();
        $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../view/frontoffice/partenaire/list.php';
    }

    // Profil d'un partenaire
    public function show() {
        if (isset($_GET['id'])) {
            $this->partenaire->id = $_GET['id'];
            
            if ($this->partenaire->getById() && $this->partenaire->statut === 'actif') {
                // Récupérer les jeux de ce partenaire
                $stmt = $this->storeItem->getByPartenaire($_GET['id']);
                $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                include __DIR__ . '/../view/frontoffice/partenaire/profile.php';
            } else {
                header("Location: ?controller=Partenaire&action=index");
                exit;
            }
        }
    }
}
?>