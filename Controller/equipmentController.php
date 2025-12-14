<?php
require_once("View/equipmentsView.php");
require_once("Model/equipmentModel.php");

class equipmentController {
    public function afficherListe() {
        $model = new equipmentModel();
        $equipments = $model->getAllEquipments();
        
        $view = new equipmentsView();
        $view->afficherListe($equipments);
    }
    
    public function afficherMesReservations() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        
        if (!$userId) {
            header("Location: index.php?router=login");
            exit();
        }
        
        $model = new equipmentModel();
        $reservations = $model->getUserReservations($userId);
        
        $view = new equipmentsView();
        $view->afficherMesReservations($reservations);
    }
    
    public function reserver() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
            
            if (!$userId) {
                header("Location: index.php?router=login");
                exit();
            }
            
            $equipId = $_POST['equip_id'] ?? 0;
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            
            $model = new equipmentModel();
            $result = $model->reserveEquipment($equipId, $userId, $dateDebut, $dateFin);
            
            if ($result) {
                header("Location: index.php?router=mes-reservationsSuccess=1");
            } else {
                header("Location: index.php?router=equipements&error=conflit");
            }
            exit();
        }
    }
    
    public function checkAvailability() {
        // AJAX availability check
        $equipId = $_GET['equip_id'] ?? 0;
        $dateDebut = $_GET['date_debut'] ?? '';
        $dateFin = $_GET['date_fin'] ?? '';
        
        $model = new equipmentModel();
        $available = $model->checkAvailability($equipId, $dateDebut, $dateFin);
        
        header('Content-Type: application/json');
        echo json_encode(['available' => $available]);
    }
}
?>
