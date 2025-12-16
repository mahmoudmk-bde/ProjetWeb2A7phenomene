<?php
// Utiliser la configuration de connexion existante du projet
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/Reclamation.php';

class ReclamationController {
    private $pdo; 
    public function __construct() {
        $this->pdo = config::getConnexion();
        $this->ensureSchema();
        $this->ensureTagsSchema();
    }

    /**
     * Ensure classification columns and indexes exist (runs automatically).
     */
    private function ensureSchema(): void {
        $sqlStatements = [
            "ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general'",
            "ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT 'General Support'",
            "CREATE INDEX IF NOT EXISTS idx_reclamation_category ON reclamation(category)",
            "CREATE INDEX IF NOT EXISTS idx_reclamation_priority ON reclamation(priorite)"
        ];
        foreach ($sqlStatements as $sql) {
            try {
                $this->pdo->exec($sql);
            } catch (Exception $e) {
                // Swallow to avoid blocking request; logs can be added later
            }
        }
    }

    private function ensureTagsSchema(): void {
        $pdo = config::getConnexion();
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS reclamation_tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reclamation_id INT NOT NULL,
                tag VARCHAR(64) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_reclamation_tag (reclamation_id, tag),
                INDEX idx_tag (tag)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Throwable $e) {
            // silent
        }
    }

    public function saveTags(int $reclamationId, array $tags): void {
        if ($reclamationId <= 0 || empty($tags)) return;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO reclamation_tags (reclamation_id, tag) VALUES (:rid, :tag)");
        foreach ($tags as $tag) {
            $t = trim($tag);
            if ($t === '') continue;
            try {
                $stmt->execute([':rid' => $reclamationId, ':tag' => $t]);
            } catch (\Throwable $e) {
                // ignore duplicates or errors
            }
        }
    }

    public function getTagsByReclamation(int $reclamationId): array {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT tag FROM reclamation_tags WHERE reclamation_id = :rid ORDER BY tag ASC");
        $stmt->execute([':rid' => $reclamationId]);
        return array_map(function($r){return $r['tag'];}, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getReclamationsByTag(string $tag): array {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT r.* FROM reclamation r 
            INNER JOIN reclamation_tags t ON t.reclamation_id = r.id 
            WHERE t.tag = :tag ORDER BY r.date_creation DESC");
        $stmt->execute([':tag' => $tag]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addReclamation(Reclamation $rec) {
        // Adapter à la structure de la table `reclamation` de projetweb3.sql
        // Les champs date_creation et updated_at ont des valeurs par défaut dans la BDD
        $sql = "INSERT INTO reclamation (sujet, description, email, statut, utilisateur_id, product_id, priorite, category, department) 
                VALUES (:sujet, :description, :email, :statut, :utilisateur_id, :product_id, :priorite, :category, :department)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sujet' => $rec->getSujet(),
            'description' => $rec->getDescription(),
            'email' => $rec->getEmail(),
            'statut' => $rec->getStatut(),
            'utilisateur_id' => $rec->getUtilisateurId(),
            'product_id' => $rec->getProductId(),
            'priorite' => $rec->getPriorite(),
            'category' => $rec->getCategory(),
            'department' => $rec->getDepartment()
        ]);
        // After insert, auto-tag
        try {
            $rid = (int)$this->pdo->lastInsertId();
            if ($rid > 0) {
                require_once __DIR__ . '/AutoTagger.php';
                $text = $rec->getSujet() . "\n" . $rec->getDescription();
                $tags = AutoTagger::extractTags($text);
                $this->saveTags($rid, $tags);
            }
        } catch (\Throwable $e) {
            // ignore tagging failure
        }
    }
    public function listReclamations() {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite, category, department 
                FROM reclamation 
                ORDER BY date_creation DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les réclamations d'un utilisateur connecté
    public function getReclamationsByUser($user_id) {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite, category, department
                FROM reclamation
                WHERE utilisateur_id = :user_id
                ORDER BY date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteReclamation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamation WHERE id=:id");
        $stmt->execute(['id' => $id]);
    }
    public function getReclamation($id) {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite, utilisateur_id, category, department
                FROM reclamation 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $statut) {
        $sql = "UPDATE reclamation SET statut = :statut, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'statut' => $statut
        ]);
    }
}
?>
