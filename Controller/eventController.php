<?php
require_once("Model/eventModel.php");
require_once("View/eventsView.php");

class eventController {
    private $model;
    
    public function __construct() {
        $this->model = new eventModel();
    }
    
    public function afficherPage() {
        $events = $this->model->getAllEvents();
        $isLoggedIn = isset($_SESSION['user_id']);
        
        // Filter: Hide internal events if not logged in
        if (!$isLoggedIn) {
            $events = array_filter($events, function($e) {
                return $e['public_cible'] !== 'interne';
            });
        }
        
        $view = new eventsView();
        $view->afficher_page($events);
    }
    
    public function afficherDetails() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?router=events');
            exit;
        }
        
        $id = $_GET['id'];
        $event = $this->model->getEventById($id);
        
        if (!$event) {
            header('Location: index.php?router=events');
            exit;
        }
        
        // Access check
        if ($event['public_cible'] === 'interne' && !isset($_SESSION['user_id'])) {
            header('Location: index.php?router=login'); // Or 403 page
            exit;
        }
        
        $view = new eventsView();
        $view->afficher_details($event);
    }
    
    public function inscrire() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['event_id'];
            
            if (isset($_SESSION['user_id'])) {
                // Auth User
                $this->model->registerUser($eventId, $_SESSION['user_id']);
            } else {
                // Guest
                $guestData = [
                    'name' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'motivation' => $_POST['motivation']
                ];
                $this->model->registerUser($eventId, null, $guestData);
            }
            
            // Redirect with success
            header("Location: index.php?router=event_details&id=$eventId&success=1");
        }
    }

    public function afficherInscrits() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?router=events');
            exit;
        }

        // Admin check
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?router=events');
            exit;
        }

        $eventId = $_GET['id'];
        $event = $this->model->getEventById($eventId);
        $registrations = $this->model->getEventRegistrations($eventId);
        $count = $this->model->getRegistrationCount($eventId);

        $view = new eventsView();
        $view->afficher_inscrits($event, $registrations, $count);
    }
}
?>
