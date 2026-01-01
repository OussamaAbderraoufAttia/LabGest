<?php
require_once("View/homeView.php");

class homeController {
    public function afficherPage() {
        $view = new homeView();
        $view->afficher_page();
    }
    
    public function afficherContact() {
        $view = new homeView();
        $view->afficherContact();
    }
    
    public function getCarouselData() {
        require_once("Model/dataBaseModel.php");
        $db = new dataBaseModel();
        $pdo = $db->connexion();
        
        $query = "SELECT * FROM carousel_items WHERE active = 1 ORDER BY ordre ASC";
        $stmt = $db->query($pdo, $query);
        $result = $stmt->get_result();
        $slides = $result->fetch_all(MYSQLI_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($slides);
        
        $db->deconnexion($pdo);
    }
    
    public function getNewsData() {
        require_once("Model/dataBaseModel.php");
        $db = new dataBaseModel();
        $pdo = $db->connexion();
        
        // News = Past Events (Recent activities "Actualités")
        $query = "SELECT e.* FROM events e 
                  WHERE e.date_event <= NOW() 
                  ORDER BY e.date_event DESC 
                  LIMIT 6";
        $stmt = $db->query($pdo, $query);
        $result = $stmt->get_result();
        $news = $result->fetch_all(MYSQLI_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($news);
        
        $db->deconnexion($pdo);
    }

    public function getEventsData() {
        require_once("Model/dataBaseModel.php");
        $db = new dataBaseModel();
        $pdo = $db->connexion();
        
        // Events = Future Events ("Événements à Venir")
        // Note: Using 2024 as fallback if NOW is too far ahead for demo data, 
        // but since we updated SQL to 2026, NOW() should work fine.
        $query = "SELECT e.* FROM events e 
                  WHERE e.date_event > NOW() 
                  ORDER BY e.date_event ASC 
                  LIMIT 6";
        $stmt = $db->query($pdo, $query);
        $result = $stmt->get_result();
        $events = $result->fetch_all(MYSQLI_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($events);
        
        $db->deconnexion($pdo);
    }
}
?>
