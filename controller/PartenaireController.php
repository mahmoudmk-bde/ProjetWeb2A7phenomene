<?php

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/Partenaire.php';
require_once __DIR__ . '/../model/StoreItem.php';

class PartenaireController
{
    private $db;
    private $partenaire;
    private $storeItem;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
        $this->partenaire = new Partenaire($this->db);
        $this->storeItem = new StoreItem($this->db);
    }

    // liste partenaires avec pagination
    public function index()
    {
        $type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : null;
        if ($type && !in_array($type, ['sponsor', 'testeur', 'vendeur'])) {
            $type = null;
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $stmt = $this->partenaire->getActifsPaginated($limit, $offset, $type);
        $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalItems = $this->partenaire->getCountActifs($type);
        $totalPages = ceil($totalItems / $limit);

        include __DIR__ . '/../view/frontoffice/partenaire/list.php';
    }

    // profil partenaire
    public function show()
    {
        if (isset($_GET['id'])) {
            $this->partenaire->id = $_GET['id'];

            if ($this->partenaire->getById() && $this->partenaire->statut === 'actif') {
                // recup jeux partenaire
                $stmt = $this->storeItem->getByPartenaire($_GET['id']);
                $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $statsTotalLikes = 0;
                $statsTotalViews = 0;
                $statsAvgPrice = null;
                $statsAvgItemRating = null;
                if (!empty($jeux)) {
                    $statsTotalLikes = array_sum(array_map(function ($j) {
                        return (int) ($j['likes_count'] ?? 0); }, $jeux));
                    $statsTotalViews = array_sum(array_map(function ($j) {
                        return (int) ($j['views_count'] ?? 0); }, $jeux));
                    $statsAvgPrice = array_sum(array_map(function ($j) {
                        return (float) ($j['prix'] ?? 0); }, $jeux)) / count($jeux);
                    $ratings = array_values(array_filter(array_map(function ($j) {
                        return isset($j['rating_avg']) ? (float) $j['rating_avg'] : null; }, $jeux), function ($v) {
                            return $v !== null; }));
                    if (count($ratings) > 0) {
                        $statsAvgItemRating = array_sum($ratings) / count($ratings);
                    }
                }
                $partnerRatingAvg = null;
                $partnerRatingCount = 0;
                // Lecture agrégée via colonnes si disponibles
                try {
                    $stmtRA = $this->db->prepare("SELECT rating_avg, rating_count FROM partenaires WHERE id = :id");
                    $stmtRA->bindParam(':id', $this->partenaire->id);
                    $stmtRA->execute();
                    $rr = $stmtRA->fetch(PDO::FETCH_ASSOC);
                    if ($rr && $rr['rating_count'] !== null) {
                        $partnerRatingAvg = (float) $rr['rating_avg'];
                        $partnerRatingCount = (int) $rr['rating_count'];
                    }
                } catch (PDOException $eA) {
                }
                try {
                    $stmtR = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM partner_ratings WHERE partenaire_id = :id");
                    $stmtR->bindParam(':id', $this->partenaire->id);
                    $stmtR->execute();
                    $r = $stmtR->fetch(PDO::FETCH_ASSOC);
                    if ($r && $partnerRatingCount === 0) {
                        $partnerRatingAvg = (float) $r['avg_score'];
                        $partnerRatingCount = (int) $r['cnt'];
                    }
                } catch (PDOException $e1) {
                    try {
                        $stmtRF = $this->db->prepare("SELECT AVG(score) as avg_score, COUNT(*) as cnt FROM partenaire_ratings WHERE partenaire_id = :id");
                        $stmtRF->bindParam(':id', $this->partenaire->id);
                        $stmtRF->execute();
                        $r = $stmtRF->fetch(PDO::FETCH_ASSOC);
                        if ($r && $partnerRatingCount === 0) {
                            $partnerRatingAvg = (float) $r['avg_score'];
                            $partnerRatingCount = (int) $r['cnt'];
                        }
                    } catch (PDOException $e2) {
                    }
                }

                include __DIR__ . '/../view/frontoffice/partenaire/profile.php';
            } else {
                header("Location: ?controller=Partenaire&action=index");
                exit;
            }
        }
    }

    public function rate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
            header("Location: ?controller=Partenaire&action=index");
            return;
        }
        $id = (int) $_GET['id'];
        $score = isset($_POST['score']) ? (int) $_POST['score'] : 0;
        $author = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
        if ($score < 1 || $score > 5) {
            header("Location: ?controller=Partenaire&action=show&id=" . $id);
            return;
        }
        // Essai sans nouvelle table: colonnes agrégées sur partenaires
        try {
            $stmtU = $this->db->prepare("UPDATE partenaires SET rating_avg = (COALESCE(rating_sum,0)+:score)/(COALESCE(rating_count,0)+1), rating_sum = COALESCE(rating_sum,0)+:score, rating_count = COALESCE(rating_count,0)+1 WHERE id = :id");
            $stmtU->bindParam(':score', $score);
            $stmtU->bindParam(':id', $id);
            $stmtU->execute();
        } catch (PDOException $eU) {
            // Fallback vers tables ratings si colonnes absentes
            try {
                $stmt = $this->db->prepare("INSERT INTO partner_ratings (partenaire_id, author_name, score, created_at) VALUES (:id, :author, :score, NOW())");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':author', $author);
                $stmt->bindParam(':score', $score);
                $stmt->execute();
            } catch (PDOException $e) {
                try {
                    $stmtF = $this->db->prepare("INSERT INTO partenaire_ratings (partenaire_id, author_name, score, created_at) VALUES (:id, :author, :score, NOW())");
                    $stmtF->bindParam(':id', $id);
                    $stmtF->bindParam(':author', $author);
                    $stmtF->bindParam(':score', $score);
                    $stmtF->execute();
                } catch (PDOException $e2) {
                }
            }
        }
        header("Location: ?controller=Partenaire&action=show&id=" . $id);
    }
}
?>