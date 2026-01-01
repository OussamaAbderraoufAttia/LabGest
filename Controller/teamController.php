<?php
require_once("View/teamsView.php");
require_once("Model/teamModel.php");
require_once("Model/userModel.php");

class teamController {
    public function afficherEquipes() {
        $model = new teamModel();
        $teams = $model->getAllTeams();
        $director = $model->getDirector();
        
        $view = new teamsView();
        $view = new teamsView();
        $view->afficherEquipes($teams, $director);
    }

    public function afficherDetailsEquipe() {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header("Location: index.php?router=equipes");
            exit();
        }

        $model = new teamModel();
        $team = $model->getTeam($id);
        
        if (!$team) {
            header("Location: index.php?router=equipes");
            exit();
        }

        $members = $model->getTeamMembers($id);
        $publications = $model->getTeamPublications($id);
        
        $view = new teamsView();
        $view->afficherDetailsEquipe($team, $members, $publications);
    }
    
    public function afficherMembreProfil() {
        $memberId = $_GET['id'] ?? 0;
        
        if (!$memberId) {
            header("Location: index.php?router=equipes");
            exit();
        }
        
        $userModel = new userModel();
        $member = $userModel->getUserById($memberId);
        
        if (!$member) {
            header("Location: index.php?router=equipes");
            exit();
        }
        
        $publications = $userModel->getUserPublications($memberId);
        $projects = $userModel->getUserProjects($memberId);
        
        $view = new teamsView();
        $view->afficherMembreProfil($member, $publications, $projects);
    }
    
    public function afficherMembrePublications() {
        // Redirect to member profile which includes publications
        $memberId = $_GET['id'] ?? 0;
        header("Location: index.php?router=membre-profil&id=" . $memberId);
        exit();
    }
    
    public function afficherEquipePublications() {
        $teamId = $_GET['id'] ?? 0;
        
        if (!$teamId) {
            header("Location: index.php?router=equipes");
            exit();
        }
        
        $model = new teamModel();
        $publications = $model->getTeamPublications($teamId);
        
        // For now, redirect to publications page
        // You could create a dedicated team publications view later
        header("Location: index.php?router=publications");
        exit();
    }
}
?>
