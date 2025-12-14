<?php
require_once("dataBaseModel.php");

class equipmentModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all equipments
    public function getAllEquipments() {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM equipments ORDER BY etat, nom";
        $result = $conn->query($query);
        $equipments = [];
        while ($row = $result->fetch_assoc()) {
            $equipments[] = $row;
        }
        $this->db->deconnexion($conn);
        return $equipments;
    }
    
    // Get equipment by ID
    public function getEquipmentById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM equipments WHERE id_equip = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $equipment = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $equipment;
    }
    
    // Check availability
    public function checkAvailability($equipId, $dateDebut, $dateFin) {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM reservations 
                  WHERE equip_id = ? 
                  AND status != 'annule'
                  AND ((date_debut BETWEEN ? AND ?) 
                       OR (date_fin BETWEEN ? AND ?)
                       OR (? BETWEEN date_debut AND date_fin))";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssss", $equipId, $dateDebut, $dateFin, $dateDebut, $dateFin, $dateDebut);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] == 0;
    }
    
    // Create reservation
    public function reserveEquipment($equipId, $userId, $dateDebut, $dateFin) {
        if (!$this->checkAvailability($equipId, $dateDebut, $dateFin)) {
            return false;
        }
        
        $conn = $this->db->connexion();
        $query = "INSERT INTO reservations (equip_id, user_id, date_debut, date_fin, status)
                  VALUES (?, ?, ?, ?, 'confirme')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $equipId, $userId, $dateDebut, $dateFin);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Get user reservations
    public function getUserReservations($userId) {
        $conn = $this->db->connexion();
        $query = "SELECT r.*, e.nom as equip_nom, e.type as equip_type
                  FROM reservations r
                  JOIN equipments e ON r.equip_id = e.id_equip
                  WHERE r.user_id = ?
                  ORDER BY r.date_debut DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = [];
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }
        $this->db->deconnexion($conn);
        return $reservations;
    }
}
?>
