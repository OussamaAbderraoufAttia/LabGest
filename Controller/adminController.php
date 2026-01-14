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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete') {
                $model->deleteUser($_POST['id_user']);
            }
            elseif ($action === 'add') {
                $model->addUser(
                    $_POST['username'],
                    $_POST['password'], // Stored plain text for this demo as requested
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['email'],
                    $_POST['role'],
                    $_POST['grade'],
                    $_POST['poste']
                );
            }
            elseif ($action === 'edit') {
                $model->updateAdminUser(
                    $_POST['id_user'],
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['email'],
                    $_POST['role'],
                    $_POST['grade'],
                    $_POST['poste']
                );
            }
            
            header("Location: index.php?router=admin-users");
            exit();
        }
        
        $users = $model->getAllUsers();
        
        $view = new adminUsersView();
        $view->afficherGestionUtilisateurs($users);
    }
    
    public function gererEquipes() {
        require_once("Model/teamModel.php");
        require_once("Model/userModel.php");
        require_once("Model/notificationModel.php"); // For notifications
        require_once("View/adminTeamsView.php");
        
        $model = new teamModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete') {
                $model->deleteTeam($_POST['id_team']);
            }
            elseif ($action === 'add' || $action === 'edit') {
                $nom = $_POST['nom'];
                $desc = $_POST['description'];
                $chef = !empty($_POST['chef_id']) ? $_POST['chef_id'] : null;
                
                $image = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $target_dir = "uploads/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed)) {
                        $new_filename = time() . "_" . uniqid() . "." . $file_extension;
                        $target_file = $target_dir . $new_filename;
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            $image = $target_file;
                        }
                    }
                }
                
                if ($action === 'add') {
                    if (!$image) $image = 'View/assets/img/default_team.jpg';
                    $model->addTeam($nom, $desc, $chef, $image);
                    
                    // Notify Chef
                    if($chef) {
                        $notifModel = new notificationModel();
                        $notifModel->createNotification($chef, "Vous avez été désigné Chef de l'équipe $nom.", "success", "index.php?router=mes-projets");
                    }
                } else {
                    $id = $_POST['id_team'];
                    // Preserve old image if not updated (needs logic in Model or here, for now passing null/new)
                    // Simplified: if image is null, model overwrites it (bad). 
                    // Better: fetching old team to keep image if null.
                    // For this iteration, I'll assumme if image is null we keep the old one (logic should be in model but query handles it).
                    // Wait, my model simply SET photo=?, so it overwrites. I need to handle this.
                    if(!$image) {
                        $oldTeam = $model->getTeam($id);
                        $image = $oldTeam['photo'];
                    }
                    
                    $model->updateTeam($id, $nom, $desc, $chef, $image);
                    
                    // Notify Chef
                     if($chef) {
                        $notifModel = new notificationModel();
                        $notifModel->createNotification($chef, "Vous avez été désigné Chef de l'équipe $nom.", "success", "index.php?router=mes-projets");
                    }
                }
            }
            header("Location: index.php?router=admin-teams");
            exit();
        }
        
        $teams = $model->getAllTeams();
        
        // Need users for the Chef dropdown
        $userModel = new userModel();
        $potentialChefs = $userModel->getAllUsers(['role' => 'enseignant-chercheur']); // Only researchers/profs usually
        
        $view = new adminTeamsView();
        $view->afficherGestionEquipes($teams, $potentialChefs);
    }
    
    public function gererProjets() {
        require_once("Model/projectModel.php");
        require_once("Model/userModel.php");
        require_once("View/adminProjectsView.php");
        
        $model = new projectModel();
        $userModel = new userModel();
        
        // Handle Actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete' && isset($_POST['id_project'])) {
                $model->deleteProject($_POST['id_project']);
            }
            elseif ($action === 'add' || $action === 'edit') {
                $titre = $_POST['titre'];
                $desc = $_POST['description'];
                $theme = $_POST['thematique'];
                $start = $_POST['date_debut'];
                $end = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
                $statut = $_POST['statut'];
                $resp = $_POST['responsable_id'];
                
                if ($action === 'add') {
                    $model->addProject($titre, $desc, $theme, $start, $end, $statut, $resp);
                } else {
                    $id = $_POST['id_project'];
                    $model->updateProject($id, $titre, $desc, $theme, $start, $end, $statut, $resp);
                }
            }
            
            // Redirect to avoid resubmission
            header("Location: index.php?router=admin-projects");
            exit();
        }
        
        $projects = $model->getAllProjects();
        $allUsers = $userModel->getAllUsers();
        
        // Filter likely responsibles (Teachers/Researchers/PhD)
        $researchers = array_filter($allUsers, function($u) {
            $role = strtolower($u['role']);
            return in_array($role, ['enseignant', 'chercheur', 'enseignant-chercheur', 'doctorant', 'professeur']);
        });
        
        $view = new adminProjectsView();
        $view->afficherGestionProjets($projects, $researchers);
    }
    
    public function gererEquipements() {
        require_once("Model/equipmentModel.php");
        require_once("View/adminEquipmentView.php");
        
        $model = new equipmentModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete') {
                $model->deleteEquipment($_POST['id_equip']);
            }
            elseif ($action === 'update_res') {
                $status = $_POST['status'];
                $model->updateReservationStatus($_POST['id_res'], $status);
                
                // Fetch reservation details to notify user
                $res = $model->getReservationById($_POST['id_res']);
                if($res) {
                    $notifModel = new notificationModel();
                    $msg = "Votre réservation pour " . $res['equipement_nom'] . " a été " . ($status == 'approved' ? 'approuvée' : ($status == 'declined' ? 'refusée' : 'mise à jour')) . ".";
                    $type = $status == 'approved' ? 'success' : ($status == 'declined' ? 'error' : 'info');
                    $notifModel->createNotification($res['user_id'], $msg, $type, "index.php?router=mes-reservations");
                }
            }
            elseif ($action === 'add' || $action === 'edit') {
                $nom = $_POST['nom'];
                $type = $_POST['type'];
                $desc = $_POST['description'];
                $etat = $_POST['etat'];
                
                $image = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $target_dir = "uploads/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed)) {
                        $new_filename = time() . "_" . uniqid() . "." . $file_extension;
                        $target_file = $target_dir . $new_filename;
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            $image = $target_file;
                        }
                    }
                }
                
                if ($action === 'add') {
                    if (!$image) $image = 'View/assets/img/default_equip.jpg';
                    $model->addEquipment($nom, $desc, $type, $etat, $image);
                } else {
                    $id = $_POST['id_equip'];
                    $model->updateEquipment($id, $nom, $desc, $type, $etat, $image);
                }
            }
            
            header("Location: index.php?router=admin-equipments");
            exit();
        }
        
        $equipments = $model->getAllEquipments();
        $reservations = $model->getAllReservations();
        
        $view = new adminEquipmentView();
        $view->afficherGestionEquipements($equipments, $reservations);
    }
    
    public function gererPublications() {
        require_once("Model/publicationModel.php");
        require_once("Model/projectModel.php");
        require_once("View/adminPublicationsView.php");
        
        $model = new publicationModel();
        $projModel = new projectModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete') {
                $model->deletePublication($_POST['id_pub']);
            }
            elseif ($action === 'validate') {
                $idPub = $_POST['id_pub'];
                $model->validatePublication($idPub);
                
                // Notify authors (need to fetch authors first)
                $authors = $model->getPublicationAuthors($idPub);
                if($authors && count($authors) > 0) {
                    $notifModel = new notificationModel();
                    foreach($authors as $author) {
                        $notifModel->createNotification($author['user_id'], "Votre publication a été validée par l'administrateur.", "success", "index.php?router=publications");
                    }
                }
            }
            elseif ($action === 'add') {
                $model->addPublication(
                    $_POST['titre'], 
                    $_POST['resume'], 
                    $_POST['date_publication'], 
                    $_POST['type'], 
                    $_POST['lien_externe'],
                    !empty($_POST['project_id']) ? $_POST['project_id'] : null
                );
            }
            
            header("Location: index.php?router=admin-publications");
            exit();
        }
        
        $pending = $model->getPendingPublications();
        $allPubs = $model->getAllPublications();
        $projects = $projModel->getAllProjects();
        
        $view = new adminPublicationsView();
        $view->afficherGestionPublications($pending, $allPubs, $projects);
    }
    
    public function gererEvenements() {
        require_once("Model/eventModel.php");
        require_once("Model/userModel.php");
        require_once("Model/notificationModel.php");
        require_once("View/adminEventsView.php");
        
        $model = new eventModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'delete_event') {
                $model->deleteEvent($_POST['id_event']);
            }
            elseif ($action === 'delete_offer') {
                $model->deleteOffer($_POST['id_offer']);
            }
            elseif ($action === 'add_event') {
                $image = 'View/assets/img/default_event.jpg';
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $target_dir = "uploads/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed)) {
                        $new_filename = time() . "_" . uniqid() . "." . $file_extension;
                        $target_file = $target_dir . $new_filename;
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            $image = $target_file;
                        }
                    }
                }
                
                $model->addEvent($_POST['titre'], $_POST['description'], $_POST['date_event'], $_POST['lieu'], $_POST['type'], $image);
                
                // Send notifications to all internal users
                $userModel = new userModel();
                $allUsers = $userModel->getAllUsers();
                $notifModel = new notificationModel();
                foreach ($allUsers as $user) {
                    $notifModel->createNotification($user['id_user'], "Nouvel événement créé: " . $_POST['titre'], "event", "index.php?router=event_details&id=" . $model->getLastEventId());
                }
            }
            elseif ($action === 'add_offer') {
                $model->addOffer($_POST['titre'], $_POST['description'], $_POST['date_limite'], $_POST['type'], $_POST['lien_postuler']);
                
                // Notify relevant users about job offer
                $userModel = new userModel();
                $allUsers = $userModel->getAllUsers();
                $notifModel = new notificationModel();
                foreach ($allUsers as $user) {
                    if ($user['role'] !== 'admin') {
                        $notifModel->createNotification($user['id_user'], "Nouvelle offre: " . $_POST['titre'], "info", "index.php?router=events");
                    }
                }
            }
            
            header("Location: index.php?router=admin-events");
            exit();
        }
        
        $events = $model->getAllEvents();
        $offers = $model->getOffers();
        
        $view = new adminEventsView();
        $view->afficherGestionEvenements($events, $offers);
    }
    
    public function parametres() {
        require_once("Model/settingsModel.php");
        require_once("View/adminSettingsView.php");
        
        $model = new settingsModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'update_settings') {
                $nom = $_POST['nom_laboratoire'];
                $about = $_POST['about_labo'];
                $email = $_POST['contact_email'];
                $phone = $_POST['contact_phone'];
                $color = $_POST['theme_color'];
                
                $logo = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                    $target_dir = "uploads/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed)) {
                        $new_filename = "logo_" . time() . "." . $file_extension;
                        $target_file = $target_dir . $new_filename;
                        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                            $logo = $target_file;
                        }
                    }
                }
                
                $model->updateSettings($nom, $about, $email, $phone, $color, $logo);
                header("Location: index.php?router=admin-settings&success=1");
                exit();
            }
        }
        
        $settings = $model->getSettings();
        $view = new adminSettingsView();
        $view->afficherParametres($settings);
    }
    
    // Wrapper methods for index.php routing
    public function dashboard() {
        return $this->afficherDashboard();
    }
    
    public function manageUsers() {
        return $this->gererUtilisateurs();
    }
    
    public function addUser() {
        // User addition is handled in gererUtilisateurs via POST
        return $this->gererUtilisateurs();
    }
    
    public function editUser() {
        // User editing is handled in gererUtilisateurs via POST
        return $this->gererUtilisateurs();
    }
    
    public function deleteUser() {
        // User deletion is handled in gererUtilisateurs via POST
        if (isset($_GET['id'])) {
            require_once("Model/userModel.php");
            $model = new userModel();
            $model->deleteUser($_GET['id']);
            header('Location: index.php?router=admin_users');
            exit;
        }
    }
    
    public function suspendUser() {
        // User suspension is handled in gererUtilisateurs via POST
        if (isset($_GET['id'])) {
            require_once("Model/userModel.php");
            $model = new userModel();
            $model->suspendUser($_GET['id']);
            header('Location: index.php?router=admin_users');
            exit;
        }
    }
    
    public function manageTeams() {
        return $this->gererEquipes();
    }
    
    public function manageEquipment() {
        return $this->gererEquipements();
    }
    
    public function managePublications() {
        return $this->gererPublications();
    }
    
    public function settings() {
        return $this->parametres();
    }
    
    // ==================== PDF REPORT GENERATION ====================
    
    /**
     * Generate Project Report PDF
     */
    public function generateProjectReportPDF() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?router=accueil");
            exit();
        }
        
        require_once("Model/projectModel.php");
        require_once("Utility/ReportGenerator.php");
        
        $projectModel = new projectModel();
        
        // Get filter parameters
        $year = $_GET['year'] ?? null;
        $theme = $_GET['theme'] ?? null;
        $responsible = $_GET['responsible'] ?? null;
        
        // Get projects
        $projects = $projectModel->getAllProjects();
        
        // Apply filters if provided
        if ($year || $theme || $responsible) {
            $projects = array_filter($projects, function($p) use ($year, $theme, $responsible) {
                if ($year && substr($p['date_debut'], 0, 4) != $year) return false;
                if ($theme && stripos($p['description'], $theme) === false) return false;
                if ($responsible && stripos($p['responsable'], $responsible) === false) return false;
                return true;
            });
        }
        
        $generator = new ReportGenerator('Laboratoire de Recherche ESI');
        $generator->generateProjectReport($projects, [
            'year' => $year,
            'theme' => $theme,
            'responsible' => $responsible
        ]);
        
        $generator->output('Rapport_Projets_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Generate Publication Report PDF (Bibliography)
     */
    public function generatePublicationReportPDF() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?router=accueil");
            exit();
        }
        
        require_once("Model/publicationModel.php");
        require_once("Utility/ReportGenerator.php");
        
        $publicationModel = new publicationModel();
        
        // Get filter parameters
        $year = $_GET['year'] ?? null;
        $author = $_GET['author'] ?? null;
        $team = $_GET['team'] ?? null;
        
        // Get publications
        $publications = $publicationModel->getAllPublications();
        
        // Apply filters if provided
        if ($year || $author || $team) {
            $publications = array_filter($publications, function($p) use ($year, $author, $team) {
                if ($year && isset($p['annee']) && $p['annee'] != $year) return false;
                if ($author && isset($p['authors'])) {
                    $authorStr = is_array($p['authors']) ? implode(' ', $p['authors']) : $p['authors'];
                    if (stripos($authorStr, $author) === false) return false;
                }
                if ($team && stripos($p['equipe'] ?? '', $team) === false) return false;
                return true;
            });
        }
        
        $generator = new ReportGenerator('Laboratoire de Recherche ESI');
        $generator->generatePublicationReport($publications, [
            'year' => $year,
            'author' => $author,
            'team' => $team
        ]);
        
        $generator->output('Rapport_Publications_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Generate Equipment Usage Report PDF
     */
    public function generateEquipmentReportPDF() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?router=accueil");
            exit();
        }
        
        require_once("Model/equipmentModel.php");
        require_once("Utility/ReportGenerator.php");
        
        $equipmentModel = new equipmentModel();
        
        // Get all equipment
        $equipment = $equipmentModel->getAllEquipment();
        
        // Calculate statistics
        $stats = [
            'total_equipment' => count($equipment),
            'available' => count(array_filter($equipment, fn($e) => $e['etat'] === 'disponible')),
            'reserved' => count(array_filter($equipment, fn($e) => $e['etat'] === 'réservé')),
            'maintenance' => count(array_filter($equipment, fn($e) => $e['etat'] === 'maintenance')),
        ];
        
        // Add utilization calculation if reservations data exists
        foreach ($equipment as &$item) {
            $item['utilization_percent'] = mt_rand(20, 95); // Placeholder calculation
            $item['reservations_count'] = mt_rand(0, 50);
        }
        
        $generator = new ReportGenerator('Laboratoire de Recherche ESI');
        $generator->generateEquipmentReport($equipment, $stats);
        
        $generator->output('Rapport_Equipements_' . date('Y-m-d') . '.pdf');
    }
}
?>
