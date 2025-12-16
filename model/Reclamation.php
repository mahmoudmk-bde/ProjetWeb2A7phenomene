<?php
class Reclamation {
    private string $sujet;
    private string $description;
    private string $email;
    private string $statut;
    private ?int $utilisateur_id;
    private ?int $product_id;
    private string $priorite;
    private ?string $category;
    private ?string $department;

    public function __construct(
        string $sujet,
        string $description,
        string $email,
        string $statut = "Non traite",
        ?int $utilisateur_id = null,
        ?int $product_id = null,
        string $priorite = "Moyenne",
        ?string $category = null,
        ?string $department = null
    ) {
        $this->sujet = $sujet;
        $this->description = $description;
        $this->email = $email;
        $this->statut = $statut;
        $this->utilisateur_id = $utilisateur_id;
        $this->product_id = $product_id;
        $this->priorite = $priorite;
        $this->category = $category;
        $this->department = $department;
    }

    public function getSujet() { return $this->sujet; }
    public function getDescription() { return $this->description; }
    public function getEmail() { return $this->email; }
    public function getStatut() { return $this->statut; }
    public function getUtilisateurId(): ?int { return $this->utilisateur_id; }
    public function getProductId(): ?int { return $this->product_id; }
    public function getPriorite(): string { return $this->priorite; }
    public function getCategory(): ?string { return $this->category; }
    public function getDepartment(): ?string { return $this->department; }
}
?>
