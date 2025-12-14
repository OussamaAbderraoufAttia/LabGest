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
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($slides);
        
        $db->deconnexion($pdo);
    }
    
    public function getNewsData() {
        require_once("Model/dataBaseModel.php");
        $db = new dataBaseModel();
        $pdo = $db->connexion();
        
        $query = "SELECT e.* FROM events e 
                  WHERE e.date_event > NOW() 
                  ORDER BY e.date_event ASC 
                  LIMIT 6";
        $stmt = $db->query($pdo, $query);
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($news);
        
        $db->deconnexion($pdo);
    }
}
?>
