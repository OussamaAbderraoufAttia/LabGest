<?php
require_once("Model/actualitesModel.php");
require_once("View/actualitesView.php");

class actualitesController {
    private $model;
    
    public function __construct() {
        $this->model = new actualitesModel();
    }
    
    public function afficherPage() {
        $actualites = $this->model->getAllActualites();
        $view = new actualitesView();
        $view->afficher_page($actualites);
    }

    public function afficherDetail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?router=actualites');
            exit;
        }

        $id = $_GET['id'];
        $actualite = $this->model->getActualiteById($id);

        if (!$actualite) {
            header('Location: index.php?router=actualites');
            exit;
        }

        $view = new actualitesView();
        $view->afficher_detail($actualite);
    }

    // Admin: View all actualites with pagination
    public function afficherAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?router=actualites');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $itemsPerPage = isset($_GET['items']) ? max(5, min(100, (int)$_GET['items'])) : 5;
        $offset = ($page - 1) * $itemsPerPage;

        $actualites = $this->model->getActualitesWithLimit($itemsPerPage, $offset);
        $total = $this->model->getTotalActualites();
        $totalPages = ceil($total / $itemsPerPage);

        $view = new actualitesView();
        $view->afficher_admin($actualites, $page, $itemsPerPage, $total, $totalPages);
    }

    // Admin: Add new actualite
    public function addActualite() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
                header('Location: index.php?router=actualites');
                exit;
            }

            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $contenu_complet = $_POST['contenu_complet'] ?? '';
            $categorie = $_POST['categorie'] ?? 'Général';
            $image_url = $_POST['image_url'] ?? 'View/assets/default_news.png';

            if ($titre && $description) {
                $this->model->addActualite($titre, $description, $contenu_complet, $categorie, $image_url, $_SESSION['user_id']);
                header('Location: index.php?router=actualites_admin&success=1');
                exit;
            }
        }

        $view = new actualitesView();
        $view->afficher_form_add();
    }

    // Admin: Edit actualite
    public function editActualite() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?router=actualites');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?router=actualites_admin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $contenu_complet = $_POST['contenu_complet'] ?? '';
            $categorie = $_POST['categorie'] ?? 'Général';
            $image_url = $_POST['image_url'] ?? 'View/assets/default_news.png';

            if ($titre && $description) {
                $this->model->updateActualite($id, $titre, $description, $contenu_complet, $categorie, $image_url);
                header('Location: index.php?router=actualites_admin&success=1');
                exit;
            }
        }

        $actualite = $this->model->getActualiteById($id);
        $view = new actualitesView();
        $view->afficher_form_edit($actualite);
    }

    // Admin: Delete actualite
    public function deleteActualite() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?router=actualites');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->deleteActualite($id);
        }

        header('Location: index.php?router=actualites_admin');
        exit;
    }

    // Admin: Archive actualite
    public function archiveActualite() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?router=actualites');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->archiveActualite($id);
        }

        header('Location: index.php?router=actualites_admin');
        exit;
    }
}
?>
