<?php
require_once("Model/notificationModel.php");

class notificationController {
    
    public function getNotifications() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }
        
        $model = new notificationModel();
        $notifs = $model->getUserNotifications($_SESSION['user_id']);
        $count = $model->getUnreadCount($_SESSION['user_id']);
        
        echo json_encode(['notifications' => $notifs, 'unread_count' => $count]);
    }
    
    public function markRead() {
        if (!isset($_POST['id'])) return;
        
        $model = new notificationModel();
        $model->markAsRead($_POST['id']);
        echo json_encode(['success' => true]);
    }
}
?>
