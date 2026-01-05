<?php
require_once("View/adminDashboardView.php");

class adminController {
    public function afficherDashboard() {
        $view = new adminDashboardView();
        $view->afficherDashboard();
    }
    
    public function gererUtilisateurs() {
        require_once("Model/userModel.php");
        require_once("View/adminUsersView.php");
        
        $model = new userModel();
        $users = $model->getAllUsers();
        
        $view = new adminUsersView();
        $view->afficherGestionUtilisateurs($users);
    }
    
    public function gererEquipes() {
        require_once("Model/teamModel.php");
        require_once("View/adminTeamsView.php");
        
        $model = new teamModel();
        $teams = $model->getAllTeams();
        
        $view = new adminTeamsView();
        $view->afficherGestionEquipes($teams);
    }
    
    public function gererProjets() {
        // Will implement - project management
    }
    
    public function gererEquipements() {
        // Will implement - equipment management
    }
    
    public function gererPublications() {
        // Will implement - publication validation
    }
    
    public function gererEvenements() {
        // Will implement - event management
    }
    
    public function parametres() {
        // Will implement - application settings
    }
}
?>
