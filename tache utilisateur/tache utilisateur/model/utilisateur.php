<?php
class Utilisateur {
    private ?int $id_util;
    private ?string $prenom;
    private ?string $nom;
    private ?DateTime $dt_naiss;
    private ?string $mail;
    private ?int $num;
    private ?string $mdp;
    private ?string $typee;
    // Constructor
    public function __construct(?int $id_util, ?string $prenom, ?string $nom, ?DateTime $dt_naiss, ?string $mail, ?int $num, ?string $mdp,?string $typee) {
        $this->id_util = $id_util;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->dt_naiss = $dt_naiss;
        $this->mail = $mail;
        $this->num = $num;
        $this->mdp = $mdp;
        $this->typee = $typee;
    }

    // Méthode pour afficher les informations de l'utilisateur
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Publication Date</th><th>Language</th><th>Status</th><th>Copies</th><th>Category</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id_util}</td>";
        echo "<td>{$this->prenom}</td>";
        echo "<td>{$this->nom}</td>";
        echo "<td>" . ($this->dt_naiss ? $this->dt_naiss->format('Y-m-d') : '') . "</td>";
        echo "<td>{$this->mail}</td>";
        echo "<td>{$this->num}</td>";
        echo "<td>{$this->mdp}</td>";
        echo "<td>{$this->typee}</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Méthode pour afficher sous forme de carte
    public function showCard() {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px; border-radius: 5px; background-color: #f9f9f9;'>";
        echo "<h3>{$this->prenom} {$this->nom}</h3>";
        echo "<p><strong>ID:</strong> {$this->id_util}</p>";
        echo "<p><strong>Date de naissance:</strong> " . ($this->dt_naiss ? $this->dt_naiss->format('d/m/Y') : '') . "</p>";
        echo "<p><strong>Email:</strong> {$this->mail}</p>";
        echo "<p><strong>Téléphone:</strong> {$this->num}</p>";
        echo "<p><strong>type:</strong> {$this->typee}</p>";
        echo "</div>";
    }

    // Getters and Setters
    public function getIdUtil(): ?int {
        return $this->id_util;
    }

    public function setIdUtil(?int $id_util): void {
        $this->id_util = $id_util;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void {
        $this->prenom = $prenom;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getDtNaiss(): ?DateTime {
        return $this->dt_naiss;
    }

    public function setDtNaiss(?DateTime $dt_naiss): void {
        $this->dt_naiss = $dt_naiss;
    }

    public function getMail(): ?string {
        return $this->mail;
    }

    public function setMail(?string $mail): void {
        $this->mail = $mail;
    }

    public function getNum(): ?int {
        return $this->num;
    }

    public function setNum(?int $num): void {
        $this->num = $num;
    }

    public function getMdp(): ?string {
        return $this->mdp;
    }

    public function setMdp(?string $mdp): void {
        $this->mdp = $mdp;
    }
    public function gettype(): ?string {
        return $this->typee;
    }

    public function settype(?string $typee): void {
        $this->typee = $typee;
    }
}


?>