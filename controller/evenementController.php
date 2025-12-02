<?php
require_once 'model/evenementModel.php';
require_once 'model/participationModel.php';

class EvenementController {
    private $eventModel;
    private $participationModel;

    public function __construct() {
        $this->eventModel = new EvenementModel();
        $this->participationModel = new ParticipationModel();
    }

    public function adminEvents() {
        // Simulation de session admin
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 'admin';
        }

        $events = $this->eventModel->getAllEvents();
        include 'view/Backoffice/evenement.php';
    }

    public function createEvent() {
        if ($_POST) {
            $titre = secure_data($_POST['titre']);
            $description = secure_data($_POST['description']);
            $date_evenement = $_POST['date_evenement'];
            $lieu = secure_data($_POST['lieu']);
            $id_organisation = $_POST['id_organisation'];
            $image = null;
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . $_FILES['image']['name'];
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // store web-root-relative path for use in front views
                    $image = '/gamingroom/' . $uploadFile;
                }
            }
            
            if ($this->eventModel->create($titre, $description, $date_evenement, $lieu, $image, $id_organisation)) {
                $_SESSION['success'] = "Événement créé avec succès!";
                header('Location: view/Backoffice/evenement.php');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la création de l'événement";
            }
        }
        include 'view/Backoffice/createevent.php';
    }

public function editEvent($id) {
    // Récupérer l'événement
    $eventData = $this->eventModel->getById($id);
    
    // Vérifier si l'événement existe
    if (!$eventData) {
        $_SESSION['error'] = "Événement non trouvé";
        header('Location: view/Backoffice/evenement.php');
        exit;
    }

    // Traitement du formulaire
    if ($_POST) {
        $titre = secure_data($_POST['titre']);
        $description = secure_data($_POST['description']);
        $date_evenement = $_POST['date_evenement'];
        $lieu = secure_data($_POST['lieu']);
        $id_organisation = $_POST['id_organisation'];
        
        // Gestion de l'image
        $image = $eventData['image']; // Image actuelle par défaut
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadDir = 'uploads/events/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . $_FILES['image']['name'];
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = '/gamingroom/' . $uploadFile;
            }
        }
        
        // Mettre à jour l'événement
        if ($this->eventModel->update($id, $titre, $description, $date_evenement, $lieu, $image, $id_organisation)) {
            $_SESSION['success'] = "Événement modifié avec succès!";
            header('Location: view/Backoffice/evenement.php');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'événement";
        }
    }
    
    // Inclure la vue
    include 'view/Backoffice/editevent.php';
}

    public function deleteEvent($id) {
        if ($this->eventModel->delete($id)) {
            $_SESSION['success'] = "Événement supprimé avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de l'événement";
        }
        header('Location: view/Backoffice/evenement.php');
        exit;
    }

    public function listEvents() {
        // Récupérer les événements actifs
        $events = $this->eventModel->getActiveEvents();
        // Compter les participants pour chaque événement
        foreach ($events as &$event) {
            $event['participants_count'] = $this->eventModel->countParticipants($event['id_evenement']);
        }
    
        // Inclure la vue en passant les variables
    include 'view/Frontoffice/evenement.php';
}

    public function eventDetails($id) {
        $event = $this->eventModel->getById($id);
        if (!$event) {
            header('Location: view/Frontoffice/evenement.php');
            exit;
        }

        $participants = $this->participationModel->getEventParticipants($id);
        
        $isRegistered = false;
        if (isset($_SESSION['user_id'])) {
            $isRegistered = $this->participationModel->isUserRegistered($_SESSION['user_id'], $id);
        }

        include 'view/Frontoffice/event_details.php';
    }

    public function participate($event_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1; // Simulation utilisateur
        }

        $event = $this->eventModel->getById($event_id);
        if (!$event) {
            $_SESSION['error'] = "Événement non trouvé";
            header('Location: view/Frontoffice/evenement.php');
            exit;
        }

        if ($this->participationModel->isUserRegistered($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = "Vous êtes déjà inscrit à cet événement";
            header('Location: view/Frontoffice/event_details.php?id=' . $event_id);
            exit;
        }

        if ($this->participationModel->create($event_id, $_SESSION['user_id'], date('Y-m-d'), 'en attente')) {
            $_SESSION['success'] = "Votre participation a été enregistrée et est en attente de validation!";
        } else {
            $_SESSION['error'] = "Erreur lors de l'inscription à l'événement";
        }

        header('Location: view/Frontoffice/event_details.php?id=' . $event_id);
        exit;
    }

    public function manageParticipations($event_id) {
        $event = $this->eventModel->getById($event_id);
        $participations = $this->participationModel->getEventParticipations($event_id);
        
        include 'view/Backoffice/participation.php';
    }

    public function updateParticipationStatus($participation_id, $status) {
        if ($this->participationModel->updateStatus($participation_id, $status)) {
            $_SESSION['success'] = "Statut de participation mis à jour!";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du statut";
        }

        header('Location: view/Backoffice/participation.php?event_id=' . $_GET['event_id']);
        exit;
    }

    public function myParticipations() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1; // Simulation utilisateur
        }

        $participations = $this->participationModel->getUserParticipations($_SESSION['user_id']);
        include 'view/Frontoffice/participation.php';
    }
}
?>