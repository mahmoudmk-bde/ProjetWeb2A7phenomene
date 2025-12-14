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

    /**
     * Valide et crée une participation
     * 
     * @param array $data Données du formulaire
     * @return array Tableau contenant 'success' (bool) et 'message' (string)
     */
    public function createWithValidation($data) {
        // Valider les données
        $validation = $this->participationModel->validateParticipation($data);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validation['errors']
            ];
        }

        // Vérifier si l'utilisateur est déjà inscrit
        if ($this->participationModel->isUserRegistered($data['id_volontaire'], $data['id_evenement'])) {
            return [
                'success' => false,
                'message' => 'Vous êtes déjà inscrit à cet événement'
            ];
        }

        // Créer la participation
        $result = $this->participationModel->create(
            $data['id_evenement'],
            $data['id_volontaire'],
            $data['date_participation'],
            $data['statut'] ?? 'en attente',
            $data['quantite'] ?? 1,
            $data['montant_total'] ?? null,
            $data['mode_paiement'] ?? null,
            $data['reference_paiement'] ?? null
        );

        if ($result) {
            return [
                'success' => true,
                'message' => 'Participation créée avec succès'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de la participation'
            ];
        }
    }
}
?>