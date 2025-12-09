<?php

class Mission
{
    private ?int $id;
    private string $titre;
    private string $jeu;
    private string $theme;
    private string $niveau_difficulte;
    private ?string $description;
    private ?string $competences_requises;

    public function __construct(
        ?int $id,
        string $titre,
        string $jeu,
        string $theme,
        string $niveau_difficulte,
        ?string $description = null,
        ?string $competences_requises = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->jeu = $jeu;
        $this->theme = $theme;
        $this->niveau_difficulte = $niveau_difficulte;
        $this->description = $description;
        $this->competences_requises = $competences_requises;
    }

    public function getId(): ?int               { return $this->id; }
    public function getTitre(): string          { return $this->titre; }
    public function getJeu(): string            { return $this->jeu; }
    public function getTheme(): string          { return $this->theme; }
    public function getNiveauDifficulte(): string { return $this->niveau_difficulte; }
    public function getDescription(): ?string   { return $this->description; }
    public function getCompetencesRequises(): ?string { return $this->competences_requises; }
    public function getMissionById($id)
{
    $sql = "SELECT * FROM missions WHERE id = ?";
    $db = config::getConnexion();
    $query = $db->prepare($sql);
    $query->execute([$id]);
    return $query->fetch();
}

}
