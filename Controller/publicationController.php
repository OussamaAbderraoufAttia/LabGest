<?php
require_once("View/publicationsView.php");
require_once("Model/publicationModel.php");

class publicationController {
    public function afficherBase() {
        $model = new publicationModel();
        
        // Get filters from GET parameters
        $filters = [];
        if (isset($_GET['year']) && $_GET['year'] !== '') {
            $filters['year'] = $_GET['year'];
        }
        if (isset($_GET['type']) && $_GET['type'] !== '') {
            $filters['type'] = $_GET['type'];
        }
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $filters['search'] = $_GET['search'];
        }
        
        $publications = $model->getAllPublications($filters);
        $years = $model->getYears();
        $types = $model->getTypes();
        
        $view = new publicationsView();
        $view->afficherBase($publications, $years, $types, $filters);
    }
    
    public function searchPublications() {
        // AJAX search endpoint
        $model = new publicationModel();
        $filters = [];
        
        if (isset($_GET['year']) && $_GET['year'] !== '') {
            $filters['year'] = $_GET['year'];
        }
        if (isset($_GET['type']) && $_GET['type'] !== '') {
            $filters['type'] = $_GET['type'];
        }
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $filters['search'] = $_GET['search'];
        }
        
        $publications = $model->getAllPublications($filters);
        
        header('Content-Type: application/json');
        echo json_encode($publications);
    }
}
?>
