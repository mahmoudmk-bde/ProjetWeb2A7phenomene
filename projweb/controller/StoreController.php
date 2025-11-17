<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/StoreItem.php';

class StoreController {
    private $db;
    private $storeItem;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->storeItem = new StoreItem($this->db);
    }

    // Afficher le store (tous les jeux)
    public function index() {
        // Filtres optionnels
        $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;
        
        $stmt = $this->storeItem->getAll();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrer par catégorie si nécessaire
        if ($categorie) {
            $items = array_filter($items, function($item) use ($categorie) {
                return $item['categorie'] === $categorie;
            });
        }
        
        include __DIR__ . '/../view/frontoffice/store/index.php';
    }

    // Détail d'un jeu
    public function show() {
        if (isset($_GET['id'])) {
            $this->storeItem->id = $_GET['id'];
            
            if ($this->storeItem->getById()) {
                // Récupérer d'autres jeux du même partenaire
                $stmt = $this->storeItem->getByPartenaire($this->storeItem->partenaire_id);
                $autresJeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Exclure le jeu actuel
                $autresJeux = array_filter($autresJeux, function($item) {
                    return $item['id'] != $this->storeItem->id;
                });
                
                include __DIR__ . '/../view/frontoffice/store/item-detail.php';
            } else {
                header("Location: ?controller=Store&action=index");
                exit;
            }
        }
    }
}
?>