<?php

class Condidature
{
    private ?int $id;
    private int $mission_id;
    private string $pseudo_gaming;
    private string $niveau_experience;
    private string $disponibilites;
    private string $email;
    private string $statut;

    public function __construct(
        ?int $id,
        int $mission_id,
        string $pseudo_gaming,
        string $niveau_experience,
        string $disponibilites,
        string $email,
        string $statut = 'en_attente',
        
    ) {
        $this->id = $id;
        $this->mission_id = $mission_id;
        $this->pseudo_gaming = $pseudo_gaming;
        $this->niveau_experience = $niveau_experience;
        $this->disponibilites = $disponibilites;
        $this->email = $email;
        $this->statut = $statut;
    }

    public function getId(): ?int              { return $this->id; }
    public function getMissionId(): int        { return $this->mission_id; }
    public function getPseudoGaming(): string  { return $this->pseudo_gaming; }
    public function getNiveauExperience(): string { return $this->niveau_experience; }
    public function getDisponibilites(): string { return $this->disponibilites; }
    public function getEmail(): string         { return $this->email; }
    public function getStatut(): string        { return $this->statut; }
}
