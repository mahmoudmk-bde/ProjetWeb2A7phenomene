<?php
// model/feedback.php
class feedback {
    private $id;
    private $id_mission;
    private $id_utilisateur;
    private $rating;
    private $commentaire;
    private $date_feedback;
    private $created_at;
    private $updated_at;

    public function __construct($id, $id_mission, $id_utilisateur, $rating, $commentaire, $date_feedback = null, $created_at = null, $updated_at = null) {
        $this->id = $id;
        $this->id_mission = $id_mission;
        $this->id_utilisateur = $id_utilisateur;
        $this->rating = $rating;
        $this->commentaire = $commentaire;
        $this->date_feedback = $date_feedback ? new DateTime($date_feedback) : new DateTime();
        $this->created_at = $created_at ? new DateTime($created_at) : new DateTime();
        $this->updated_at = $updated_at ? new DateTime($updated_at) : new DateTime();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getIdMission() { return $this->id_mission; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getRating() { return $this->rating; }
    public function getCommentaire() { return $this->commentaire; }
    public function getDateFeedback() { return $this->date_feedback; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters
    public function setRating($rating) { $this->rating = $rating; }
    public function setCommentaire($commentaire) { $this->commentaire = $commentaire; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }
}
?>