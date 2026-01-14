<?php
require_once("View/projectsView.php");
require_once("Model/projectModel.php");

class projectController {
    private $projectModel;
    
    public function __construct() {
        $this->projectModel = new projectModel();
    }

    // ============ PERMISSION HELPERS ============
    private function canManageMembers($projectId) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        $userRole = $_SESSION['admin']['role'] ?? $_SESSION['user']['role'] ?? null;
        
        return ($userRole === 'admin') || $this->projectModel->isResponsable($projectId, $userId);
    }

    private function isProjectMember($projectId, $userId) {
        return $this->projectModel->isProjectMember($projectId, $userId);
    }

    // ============ PUBLIC ACTIONS ============
    
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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $model = new projectModel();
        $project = $model->getProjectById($id);
        
        if (!$project) {
            header("Location: index.php?router=projets");
            exit();
        }
        
        $members = $model->getProjectMembers($id);
        $publications = $model->getProjectPublications($id);
        $partners = $model->getProjectPartners($id);
        
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        $userRole = $_SESSION['admin']['role'] ?? $_SESSION['user']['role'] ?? null;
        $isLoggedIn = !empty($userId);
        $isMember = $isLoggedIn && $model->isProjectMember($id, $userId);
        $isResponsable = $isLoggedIn && $model->isResponsable($id, $userId);
        
        $view = new projectsView();
        $view->afficherDetails($project, $members, $publications, $partners, $isLoggedIn, $isMember, $isResponsable);
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

    // ============ MEMBER MANAGEMENT ACTIONS ============

    public function addMember() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit();
        }

        $projectId = $_POST['project_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $roleProjet = $_POST['role_projet'] ?? 'Membre';

        if (!$projectId || !$userId) {
            header("HTTP/1.0 400 Bad Request");
            exit();
        }

        if (!$this->canManageMembers($projectId)) {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }

        $result = $this->projectModel->addProjectMember($projectId, $userId, $roleProjet);
        
        if ($result) {
            // Create notification
            require_once("Model/notificationModel.php");
            $notifModel = new notificationModel();
            $project = $this->projectModel->getProjectById($projectId);
            $notifModel->createNotification($userId, "Vous avez été ajouté au projet: " . $project['titre'], "team", "index.php?router=projet-details&id=" . $projectId);
        }

        header("Location: index.php?router=projet-details&id=" . $projectId);
        exit();
    }

    public function removeMember() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit();
        }

        $projectId = $_POST['project_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;

        if (!$projectId || !$userId) {
            header("HTTP/1.0 400 Bad Request");
            exit();
        }

        if (!$this->canManageMembers($projectId)) {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }

        // Responsable cannot remove themselves
        if ($this->projectModel->isResponsable($projectId, $userId)) {
            $currentUserId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
            if ($currentUserId == $userId && $_SESSION['admin']['role'] ?? $_SESSION['user']['role'] ?? null !== 'admin') {
                header("Location: index.php?router=projet-details&id=" . $projectId . "&error=cannot-remove-self");
                exit();
            }
        }

        $this->projectModel->removeProjectMember($projectId, $userId);

        header("Location: index.php?router=projet-details&id=" . $projectId);
        exit();
    }

    public function quitProject() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit();
        }

        $projectId = $_POST['project_id'] ?? null;
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;

        if (!$projectId || !$userId) {
            header("HTTP/1.0 400 Bad Request");
            exit();
        }

        // Responsable cannot quit by themselves (must transfer responsibility first)
        if ($this->projectModel->isResponsable($projectId, $userId)) {
            header("Location: index.php?router=projet-details&id=" . $projectId . "&error=responsable-cannot-quit");
            exit();
        }

        $this->projectModel->removeProjectMember($projectId, $userId);

        header("Location: index.php?router=projets");
        exit();
    }

    public function closeProject() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit();
        }

        $projectId = $_POST['project_id'] ?? null;

        if (!$projectId) {
            header("HTTP/1.0 400 Bad Request");
            exit();
        }

        if (!$this->canManageMembers($projectId)) {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }

        $this->projectModel->closeProject($projectId);

        header("Location: index.php?router=projet-details&id=" . $projectId);
        exit();
    }

    // ============ PUBLICATION ACTIONS ============

    public function addPublication() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit();
        }

        $projectId = $_POST['project_id'] ?? null;
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;

        if (!$projectId || !$userId) {
            header("HTTP/1.0 400 Bad Request");
            exit();
        }

        // User must be project member
        if (!$this->isProjectMember($projectId, $userId)) {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }

        require_once("Model/publicationModel.php");
        $pubModel = new publicationModel();

        $data = [
            'titre' => $_POST['titre'] ?? '',
            'resume' => $_POST['resume'] ?? '',
            'type' => $_POST['type'] ?? 'article',
            'date_publication' => $_POST['date_publication'] ?? date('Y-m-d'),
            'doi' => $_POST['doi'] ?? null,
            'project_id' => $projectId,
            'team_id' => $_POST['team_id'] ?? null
        ];

        $authorIds = [];
        if (isset($_POST['authors']) && is_array($_POST['authors'])) {
            $authorIds = array_filter($_POST['authors']);
        }

        // Add primary author (current user) if not already included
        if (!in_array($userId, $authorIds)) {
            array_unshift($authorIds, $userId);
        }

        $pubId = $pubModel->addPublicationWithAuthors($data, $authorIds);

        if ($pubId) {
            require_once("Model/notificationModel.php");
            $notifModel = new notificationModel();
            $project = $this->projectModel->getProjectById($projectId);
            $notifModel->createNotification($userId, "Nouvelle publication ajoutée au projet: " . $project['titre'], "article", "index.php?router=publications");
        }

        header("Location: index.php?router=projet-details&id=" . $projectId);
        exit();
    }
}
?>
