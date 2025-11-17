<?php
class Article {
    private ?int $id_article;
    private ?string $titre;
    private ?string $contenu;
    private ?DateTime $date_publication;

    // Constructeur
    public function __construct(?int $id_article, ?string $titre, ?string $contenu, ?DateTime $date_publication) {
        $this->id_article = $id_article;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_publication = $date_publication;
    }

    // Méthode pour afficher l'article dans un tableau HTML
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID Article</th><th>Titre</th><th>Contenu</th><th>Date Publication</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id_article}</td>";
        echo "<td>{$this->titre}</td>";
        echo "<td>{$this->contenu}</td>";
        echo "<td>" . ($this->date_publication ? $this->date_publication->format('Y-m-d') : '') . "</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters et Setters
    public function getIdArticle(): ?int {
        return $this->id_article;
    }

    public function setIdArticle(?int $id_article): void {
        $this->id_article = $id_article;
    }

    public function getTitre(): ?string {
        return $this->titre;
    }

    public function setTitre(?string $titre): void {
        $this->titre = $titre;
    }

    public function getContenu(): ?string {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): void {
        $this->contenu = $contenu;
    }

    public function getDatePublication(): ?DateTime {
        return $this->date_publication;
    }

    public function setDatePublication(?DateTime $date_publication): void {
        $this->date_publication = $date_publication;
    }
}

class Quiz {
    private ?int $id_quiz;
    private ?string $question;
    private ?string $reponse1;
    private ?string $reponse2;
    private ?string $reponse3;
    private ?string $bonne_reponse;
    private ?int $id_article;

    // Constructeur
    public function __construct(?int $id_quiz, ?string $question, ?string $reponse1, ?string $reponse2, ?string $reponse3, ?string $bonne_reponse, ?int $id_article) {
        $this->id_quiz = $id_quiz;
        $this->question = $question;
        $this->reponse1 = $reponse1;
        $this->reponse2 = $reponse2;
        $this->reponse3 = $reponse3;
        $this->bonne_reponse = $bonne_reponse;
        $this->id_article = $id_article;
    }

    // Méthode pour afficher le quiz dans un tableau HTML
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID Quiz</th><th>Question</th><th>Réponse 1</th><th>Réponse 2</th><th>Réponse 3</th><th>Bonne Réponse</th><th>ID Article</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id_quiz}</td>";
        echo "<td>{$this->question}</td>";
        echo "<td>{$this->reponse1}</td>";
        echo "<td>{$this->reponse2}</td>";
        echo "<td>{$this->reponse3}</td>";
        echo "<td>{$this->bonne_reponse}</td>";
        echo "<td>{$this->id_article}</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters et Setters
    public function getIdQuiz(): ?int {
        return $this->id_quiz;
    }

    public function setIdQuiz(?int $id_quiz): void {
        $this->id_quiz = $id_quiz;
    }

    public function getQuestion(): ?string {
        return $this->question;
    }

    public function setQuestion(?string $question): void {
        $this->question = $question;
    }

    public function getReponse1(): ?string {
        return $this->reponse1;
    }

    public function setReponse1(?string $reponse1): void {
        $this->reponse1 = $reponse1;
    }

    public function getReponse2(): ?string {
        return $this->reponse2;
    }

    public function setReponse2(?string $reponse2): void {
        $this->reponse2 = $reponse2;
    }

    public function getReponse3(): ?string {
        return $this->reponse3;
    }

    public function setReponse3(?string $reponse3): void {
        $this->reponse3 = $reponse3;
    }

    public function getBonneReponse(): ?string {
        return $this->bonne_reponse;
    }

    public function setBonneReponse(?string $bonne_reponse): void {
        $this->bonne_reponse = $bonne_reponse;
    }

    public function getIdArticle(): ?int {
        return $this->id_article;
    }

    public function setIdArticle(?int $id_article): void {
        $this->id_article = $id_article;
    }
}

?>