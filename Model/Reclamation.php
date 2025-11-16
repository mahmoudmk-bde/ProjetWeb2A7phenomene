<?php
class Reclamation {
    private string $sujet;
    private string $description;
    private string $email;
    private string $statut;

    public function __construct($sujet, $description, $email, $statut = "Non traité") {
        $this->sujet = $sujet;
        $this->description = $description;
        $this->email = $email;
        $this->statut = $statut;
    }

    public function getSujet() { return $this->sujet; }
    public function getDescription() { return $this->description; }
    public function getEmail() { return $this->email; }
    public function getStatut() { return $this->statut; }
}
?>
