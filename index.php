<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define root path
define('ROOT', __DIR__);

// Include controllers
require_once("Controller/homeController.php");
require_once("Controller/userController.php");
require_once("Controller/projectController.php");
require_once("Controller/publicationController.php");
require_once("Controller/equipmentController.php");
require_once("Controller/teamController.php");
require_once("Controller/eventController.php");
require_once("Controller/adminController.php");

// Routing
if (isset($_GET['router'])) {
    $action = $_GET['router'];
    
    switch ($action) {
        // ============= PUBLIC PAGES =============
        case 'accueil':
            $controller = new homeController();
            $controller->afficherPage();
            break;
            
        case 'projets':
            $controller = new projectController();
            $controller->afficherCatalogue();
            break;
            
        case 'projet-details':
            if (isset($_GET['id'])) {
                $controller = new projectController();
                $controller->afficherDetails($_GET['id']);
            }
            break;
            
        case 'publications':
            $controller = new publicationController();
            $controller->afficherBase();
            break;
            
        case 'equipements':
            $controller = new equipmentController();
            $controller->afficherListe();
            break;
            
        case 'equipe-details':
            require_once("Controller/teamController.php");
            $controller = new teamController();
            $controller->afficherDetailsEquipe();
            break;

        case 'add-team-member':
            require_once("Controller/teamController.php");
            $controller = new teamController();
            $controller->addTeamMember();
            break;

        case 'remove-team-member':
            require_once("Controller/teamController.php");
            $controller = new teamController();
            $controller->removeTeamMember();
            break;

        case 'leave-team':
            require_once("Controller/teamController.php");
            $controller = new teamController();
            $controller->leaveTeam();
            break;

        case 'membre-profil':
            require_once("Controller/teamController.php");
            $controller = new teamController();
            $controller->afficherMembreProfil();
            break;
            
        case 'equipes':
            $controller = new teamController();
            $controller->afficherEquipes();
            break;
            
        case 'events': // Changed from 'evenements' to match controller logic
            require_once("Controller/eventController.php");
            $controller = new eventController();
            $controller->afficherPage();
            break;
            
        case 'event_details':
            require_once("Controller/eventController.php");
            $controller = new eventController();
            $controller->afficherDetails();
            break;
            
        case 'event_register':
            require_once("Controller/eventController.php");
            $controller = new eventController();
            $controller->inscrire();
            break;
            
        case 'event_inscrits':
            require_once("Controller/eventController.php");
            $controller = new eventController();
            $controller->afficherInscrits();
            break;

        case 'actualites':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->afficherPage();
            break;

        case 'actualite_detail':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->afficherDetail();
            break;

        case 'actualites_admin':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->afficherAdmin();
            break;

        case 'actualite_add':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->addActualite();
            break;

        case 'actualite_edit':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->editActualite();
            break;

        case 'actualite_delete':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->deleteActualite();
            break;

        case 'actualite_archive':
            require_once("Controller/actualitesController.php");
            $controller = new actualitesController();
            $controller->archiveActualite();
            break;
            
        case 'contact':
            $controller = new homeController();
            $controller->afficherContact();
            break;
            
        // ============= AUTHENTICATION =============
        case 'login':
            $controller = new userController();
            $controller->afficherPageLogin();
            break;
            
        case 'process-login':
            $controller = new userController();
            $controller->login();
            break;
            
        case 'logout':
            $controller = new userController();
            $controller->logout();
            break;
            
        // ============= USER PROFILE =============
        case 'profil':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new userController();
                $controller->afficherProfil();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;
            
        case 'mes-projets':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->afficherMesProjets();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;
            
        case 'mes-reservations':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new equipmentController();
                $controller->afficherMesReservations();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;

        // ============= PROJECT MANAGEMENT =============
        case 'project-add-member':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->addMember();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;

        case 'project-remove-member':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->removeMember();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;

        case 'project-quit':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->quitProject();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;

        case 'project-close':
            if (isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->closeProject();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;

        case 'project-add-publication':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new projectController();
                $controller->addPublication();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;
            
        case 'update-profile':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new userController();
                $controller->modifyPersoInfo();
            }
            break;
            
        case 'update-photo':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new userController();
                $controller->modifyPdp();
            }
            break;
            
        case 'update-password':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new userController();
                $controller->modifyPassword();
            }
            break;
            
        // ============= RESERVATIONS =============
        case 'reserver-equipement':
            if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
                $controller = new equipmentController();
                $controller->reserver();
            } else {
                header("Location: index.php?router=login");
                exit();
            }
            break;
            
        // ============= ADMIN DASHBOARD =============
        case 'admin':
        case 'admin_dashboard':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->dashboard();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= ADMIN USERS =============
        case 'admin_users':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->manageUsers();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_users_add':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->addUser();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_users_edit':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->editUser();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_users_delete':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->deleteUser();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_users_suspend':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->suspendUser();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= ADMIN TEAMS =============
        case 'admin_teams':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->manageTeams();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= ADMIN EQUIPMENT =============
        case 'admin_equipment':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->manageEquipment();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= ADMIN PUBLICATIONS =============
        case 'admin_publications':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->managePublications();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= ADMIN SETTINGS =============
        case 'admin_settings':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->settings();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        // ============= PDF EXPORT ROUTES =============
        case 'admin_report_projects_pdf':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->generateProjectReportPDF();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_report_publications_pdf':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->generatePublicationReportPDF();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;

        case 'admin_report_equipment_pdf':
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
                require_once("Controller/adminController.php");
                $controller = new adminController();
                $controller->generateEquipmentReportPDF();
            } else {
                header('Location: index.php?router=accueil');
                exit;
            }
            break;
            
        // ============= LEGACY ADMIN ROUTES (keep for compatibility) =============
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->afficherDashboard();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-users':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererUtilisateurs();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-teams':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererEquipes();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-projects':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererProjets();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-equipments':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererEquipements();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-publications':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererPublications();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-events':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->gererEvenements();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        case 'admin-settings':
            if (isset($_SESSION['admin'])) {
                $controller = new adminController();
                $controller->parametres();
            } else {
                header("Location: index.php?router=accueil");
                exit();
            }
            break;
            
        // ============= AJAX ENDPOINTS =============
        case 'get-carousel':
            $controller = new homeController();
            $controller->getCarouselData();
            break;
            
        case 'get-news':
            $controller = new homeController();
            $controller->getNewsData();
            break;

        case 'get-events':
            $controller = new homeController();
            $controller->getEventsData();
            break;
            
        case 'filter-projects':
            $controller = new projectController();
            $controller->filterProjects();
            break;
            
        case 'search-publications':
            $controller = new publicationController();
            $controller->searchPublications();
            break;
            
        case 'check-availability':
            $controller = new equipmentController();
            $controller->checkAvailability();
            break;
            
        case 'get-notifications':
            require_once("Controller/notificationController.php");
            $controller = new notificationController();
            $controller->getNotifications();
            break;
            
        case 'mark-notification-read':
            require_once("Controller/notificationController.php");
            $controller = new notificationController();
            $controller->markRead();
            break;

        // ============= DEFAULT =============
        default:
            $controller = new homeController();
            $controller->afficherPage();
            break;
    }
} else {
    // Default route - homepage
    $controller = new homeController();
    $controller->afficherPage();
}
?>
