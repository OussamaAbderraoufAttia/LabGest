<?php
require_once("dataBaseModel.php");

class notificationModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    public function getUserNotifications($userId) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifs = $result->fetch_all(MYSQLI_ASSOC);
        $this->db->deconnexion($conn);
        return $notifs;
    }
    
    public function getUnreadCount($userId) {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $this->db->deconnexion($conn);
        return $result['count'];
    }
    
    public function createNotification($userId, $message, $type, $link = '#') {
        $conn = $this->db->connexion();
        $query = "INSERT INTO notifications (user_id, message, type, link, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $userId, $message, $type, $link);
        $stmt->execute();
        $this->db->deconnexion($conn);
    }
    
    public function markAsRead($id) {
        $conn = $this->db->connexion();
        $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->db->deconnexion($conn);
    }
}
?>
