<?php

class Condidature
{
    private ?int $id;
    private int $mission_id;
    private ?int $volontaire_id;
    private string $pseudo_gaming;
    private string $niveau_experience;
    private string $disponibilites;
    private string $email;
    private string $statut;
    private ?string $created_at;

    public function __construct(
        ?int $id,
        int $mission_id,
        ?int $volontaire_id,
        string $pseudo_gaming,
        string $niveau_experience,
        string $disponibilites,
        string $email,
        string $statut = 'en_attente',
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->mission_id = $mission_id;
        $this->volontaire_id = $volontaire_id;
        $this->pseudo_gaming = $pseudo_gaming;
        $this->niveau_experience = $niveau_experience;
        $this->disponibilites = $disponibilites;
        $this->email = $email;
        $this->statut = $statut;
        $this->created_at = $created_at;
    }

    public function getId(): ?int              { return $this->id; }
    public function getMissionId(): int        { return $this->mission_id; }
    public function getPseudoGaming(): string  { return $this->pseudo_gaming; }
    public function getNiveauExperience(): string { return $this->niveau_experience; }
    public function getDisponibilites(): string { return $this->disponibilites; }
    public function getEmail(): string         { return $this->email; }
    public function getStatut(): string        { return $this->statut; }
}
