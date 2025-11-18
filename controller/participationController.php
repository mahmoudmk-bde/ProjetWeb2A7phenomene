<?php
require_once 'model/participationModel.php';

class ParticipationController {
    private $participationModel;

    public function __construct() {
        $this->participationModel = new ParticipationModel();
    }

    // Méthodes spécifiques aux participations si besoin
    public function getUserStats($user_id) {
        $participations = $this->participationModel->getUserParticipations($user_id);
        
        $stats = [
            'total' => count($participations),
            'acceptees' => count(array_filter($participations, function($p) { return $p['statut'] == 'acceptée'; })),
            'en_attente' => count(array_filter($participations, function($p) { return $p['statut'] == 'en attente'; })),
            'refusees' => count(array_filter($participations, function($p) { return $p['statut'] == 'refusée'; }))
        ];
        
        return $stats;
    }
}
?>