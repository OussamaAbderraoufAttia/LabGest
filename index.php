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
            
        case 'equipes':
            $controller = new teamController();
            $controller->afficherEquipes();
            break;
            
        case 'evenements':
            $controller = new eventController();
            $controller->afficherEvenements();
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
        case 'admin-dashboard':
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
