<?php
class Response {
    private int $reclamation_id;
    private string $contenu;

    public function __construct($reclamation_id, $contenu) {
        $this->reclamation_id = $reclamation_id;
        $this->contenu = $contenu;
    }

    public function getReclamationId() { return $this->reclamation_id; }
    public function getContenu() { return $this->contenu; }
}
?>
