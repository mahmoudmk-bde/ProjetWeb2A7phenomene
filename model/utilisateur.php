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
    private ?string $q1;
    private ?string $rp1;
    private ?string $q2;
    private ?string $rp2;
    private ?string $auth;
    private ?string $img;
    private ?int $score;
    private ?string $oauth_provider;
    private ?string $oauth_uid;
    private ?string $face;
    
    // Constructor
    public function __construct(?int $id_util, ?string $prenom, ?string $nom, ?DateTime $dt_naiss, ?string $mail, ?int $num, ?string $mdp, ?string $typee, ?string $q1 = '', ?string $rp1 = '', ?string $q2 = '', ?string $rp2 = '', ?string $auth = '', ?string $img = '', ?int $score = 0, ?string $oauth_provider = null, ?string $oauth_uid = null, ?string $face = null) {
        $this->id_util = $id_util;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->dt_naiss = $dt_naiss;
        $this->mail = $mail;
        $this->num = $num;
        $this->mdp = $mdp;
        $this->typee = $typee;
        $this->q1 = $q1;
        $this->rp1 = $rp1;
        $this->q2 = $q2;
        $this->rp2 = $rp2;
        $this->auth = $auth;
        $this->img = $img;
        $this->score = $score;
        $this->oauth_provider = $oauth_provider;
        $this->oauth_uid = $oauth_uid;
    }

    // Méthode pour afficher les informations de l'utilisateur
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Date de naissance</th><th>Email</th><th>Téléphone</th><th>Type</th><th>Auth</th><th>Image</th><th>Score</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id_util}</td>";
        echo "<td>{$this->prenom}</td>";
        echo "<td>{$this->nom}</td>";
        echo "<td>" . ($this->dt_naiss ? $this->dt_naiss->format('Y-m-d') : '') . "</td>";
        echo "<td>{$this->mail}</td>";
        echo "<td>{$this->num}</td>";
        echo "<td>{$this->typee}</td>";
        echo "<td>{$this->auth}</td>";
        echo "<td>{$this->img}</td>";
        echo "<td>{$this->score}</td>";
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
        echo "<p><strong>Type:</strong> {$this->typee}</p>";
        echo "<p><strong>Auth:</strong> {$this->auth}</p>";
        echo "<p><strong>Image:</strong> {$this->img}</p>";
        echo "<p><strong>Score:</strong> {$this->score}</p>";
        echo "</div>";
    }

    // Getters et Setters
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

    public function getType(): ?string {
        return $this->typee;
    }

    public function setType(?string $typee): void {
        $this->typee = $typee;
    }

    public function getQ1(): ?string {
        return $this->q1;
    }

    public function setQ1(?string $q1): void {
        $this->q1 = $q1;
    }

    public function getRp1(): ?string {
        return $this->rp1;
    }

    public function setRp1(?string $rp1): void {
        $this->rp1 = $rp1;
    }

    public function getQ2(): ?string {
        return $this->q2;
    }

    public function setQ2(?string $q2): void {
        $this->q2 = $q2;
    }

    public function getRp2(): ?string {
        return $this->rp2;
    }

    public function setRp2(?string $rp2): void {
        $this->rp2 = $rp2;
    }

    public function getAuth(): ?string {
        return $this->auth;
    }

    public function setAuth(?string $auth): void {
        $this->auth = $auth;
    }

    public function getImg(): ?string {
        return $this->img;
    }

    public function setImg(?string $img): void {
        $this->img = $img;
    }

    public function getScore(): ?int {
        return $this->score;
    }

    public function setScore(?int $score): void {
        $this->score = $score;
    }

    public function getOauthProvider(): ?string {
        return $this->oauth_provider;
    }

    public function setOauthProvider(?string $oauth_provider): void {
        $this->oauth_provider = $oauth_provider;
    }

    public function getOauthUid(): ?string {
        return $this->oauth_uid;
    }

    public function setOauthUid(?string $oauth_uid): void {
        $this->oauth_uid = $oauth_uid;
    }

    public function getFace(): ?string {
        return $this->face;
    }

    public function setFace(?string $face): void {
        $this->face = $face;
    }
}
?>