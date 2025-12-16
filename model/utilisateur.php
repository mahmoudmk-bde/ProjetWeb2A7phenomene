<?php
class Utilisateur {
    private ?int $id_util;
    private ?string $prenom;
    private ?string $nom;
    private ?DateTime $dt_naiss;
    private ?string $mail;
    private ?string $num;
    private ?string $mdp;
    private ?string $typee;
    private ?string $q1;
    private ?string $rp1;
    private ?string $q2;
    private ?string $rp2;
    private ?string $img;
    private ?string $auth;
    private ?string $face;

    public function __construct(
        $prenom, $nom, $dt_naiss, $mail, $num, $mdp, $typee,
        $q1, $rp1, $q2, $rp2, $auth = 'desactive', $img = null, $face = null
    ) {
        $this->prenom = $prenom;
        $this->nom = $nom;

        // Convertir la date au format DateTime
        $this->dt_naiss = $dt_naiss ? new DateTime($dt_naiss) : null;

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
        $this->face = $face;
    }

    public function getPrenom() { return $this->prenom; }
    public function getNom() { return $this->nom; }

    public function getDtNaiss(): ?string {
        return $this->dt_naiss ? $this->dt_naiss->format('Y-m-d') : null;
    }

    public function getMail() { return $this->mail; }
    public function getNum() { return $this->num; }
    public function getMdp() { return $this->mdp; }
    public function getTypee() { return $this->typee; }

    public function getQ1() { return $this->q1; }
    public function getRp1() { return $this->rp1; }
    public function getQ2() { return $this->q2; }
    public function getRp2() { return $this->rp2; }
    public function getAuth() { return $this->auth; }
    public function getImg() { return $this->img; }
    public function getFace() { return $this->face; }
}
?>
