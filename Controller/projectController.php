<?php
require_once("View/projectsView.php");
require_once("Model/projectModel.php");

class projectController {
    public function afficherCatalogue() {
        $model = new projectModel();
        
        // Get filters from GET parameters
        $filters = [];
        if (isset($_GET['thematique'])) {
            $filters['thematique'] = $_GET['thematique'];
        }
        if (isset($_GET['statut'])) {
            $filters['statut'] = $_GET['statut'];
        }
        
        $projects = $model->getAllProjects($filters);
        $themes = $model->getThemes();
        
        $view = new projectsView();
        $view->afficherCatalogue($projects, $themes, $filters);
    }
    
    public function afficherDetails($id) {
        $model = new projectModel();
        $project = $model->getProjectById($id);
        
        if (!$project) {
            header("Location: index.php?router=projets");
            exit();
        }
        
        $members = $model->getProjectMembers($id);
        $publications = $model->getProjectPublications($id);
        $partners = $model->getProjectPartners($id);
        
        $view = new projectsView();
        $view->afficherDetails($project, $members, $publications, $partners);
    }
    
    public function afficherMesProjets() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        
        if (!$userId) {
            header("Location: index.php?router=login");
            exit();
        }
        
        require_once("Model/userModel.php");
        $userModel = new userModel();
        $projects = $userModel->getUserProjects($userId);
        
        $view = new projectsView();
        $view->afficherMesProjets($projects);
    }
    
    public function filterProjects() {
        // AJAX filtering endpoint
        $model = new projectModel();
        $filters = [];
        
        if (isset($_GET['thematique']) && $_GET['thematique'] !== '') {
            $filters['thematique'] = $_GET['thematique'];
        }
        if (isset($_GET['statut']) && $_GET['statut'] !== '') {
            $filters['statut'] = $_GET['statut'];
        }
        
        $projects = $model->getAllProjects($filters);
        
        header('Content-Type: application/json');
        echo json_encode($projects);
    }
}
?>
