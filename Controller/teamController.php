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

    // Add member to team (chef only)
    public function addTeamMember() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'] ?? 0;
            $userId = $_POST['user_id'] ?? 0;
            $currentUserId = $_SESSION['user']['id_user'] ?? null;
            $isAdmin = isset($_SESSION['admin']);

            $model = new teamModel();

            // Check if current user is chef or admin
            if (!$model->isTeamChef($teamId, $currentUserId) && !$isAdmin) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=not_chef");
                exit();
            }

            // Add member
            if ($model->addTeamMember($teamId, $userId, 'Membre')) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&success=member_added");
            } else {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=already_member");
            }
            exit();
        }
    }

    // Remove member from team (chef can remove others but not self, admin can remove anyone)
    public function removeTeamMember() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'] ?? 0;
            $userId = $_POST['user_id'] ?? 0;
            $currentUserId = $_SESSION['user']['id_user'] ?? null;
            $isAdmin = isset($_SESSION['admin']);

            $model = new teamModel();

            // Check if current user is chef or admin
            if (!$model->isTeamChef($teamId, $currentUserId) && !$isAdmin) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=not_chef");
                exit();
            }

            // Chef cannot delete themselves, but admin can delete anyone (including chef)
            if (!$isAdmin && $model->isTeamChef($teamId, $userId)) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=cannot_delete_chef");
                exit();
            }

            // Remove member
            if ($model->removeTeamMember($teamId, $userId)) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&success=member_removed");
            } else {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=remove_failed");
            }
            exit();
        }
    }

    // Member leaves team
    public function leaveTeam() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'] ?? 0;
            $currentUserId = $_SESSION['user']['id_user'] ?? null;

            $model = new teamModel();

            // Cannot leave if you're the chef
            if ($model->isTeamChef($teamId, $currentUserId)) {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=chef_cannot_leave");
                exit();
            }

            // Leave team
            if ($model->removeTeamMember($teamId, $currentUserId)) {
                header("Location: index.php?router=equipes&success=left_team");
            } else {
                header("Location: index.php?router=equipe-details&id=" . $teamId . "&error=leave_failed");
            }
            exit();
        }
    }
}
?>
