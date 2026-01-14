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
    // Add Equipment
    public function addEquipment($nom, $description, $type, $etat, $image) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO equipments (nom, description, type, etat, image_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $nom, $description, $type, $etat, $image);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Update Equipment
    public function updateEquipment($id, $nom, $description, $type, $etat, $image = null) {
        $conn = $this->db->connexion();
        if ($image) {
            $query = "UPDATE equipments SET nom=?, description=?, type=?, etat=?, image_url=? WHERE id_equip=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $nom, $description, $type, $etat, $image, $id);
        } else {
            $query = "UPDATE equipments SET nom=?, description=?, type=?, etat=? WHERE id_equip=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $nom, $description, $type, $etat, $id);
        }
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete Equipment
    public function deleteEquipment($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM equipments WHERE id_equip = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Get ALL Reservations (Admin)
    public function getAllReservations() {
        $conn = $this->db->connexion();
        $query = "SELECT r.*, e.nom as equip_nom, u.nom as user_nom, u.prenom as user_prenom 
                  FROM reservations r
                  JOIN equipments e ON r.equip_id = e.id_equip
                  JOIN users u ON r.user_id = u.id_user
                  ORDER BY r.date_debut DESC";
        $result = $conn->query($query);
        $reservations = [];
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }
        $this->db->deconnexion($conn);
        return $reservations;
    }

    // Get Reservation by ID (for notifications details)
    public function getReservationById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT r.*, e.nom as equipement_nom, u.email 
                  FROM reservations r
                  JOIN equipments e ON r.equip_id = e.id_equip
                  JOIN users u ON r.user_id = u.id_user
                  WHERE r.id_reservation = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $reservation;
    }

    // Update Reservation Status
    public function updateReservationStatus($id, $status) {
        $conn = $this->db->connexion();
        $query = "UPDATE reservations SET status = ? WHERE id_reservation = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Get total equipment count
    public function getTotalEquipment() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM equipments";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }

    // Get equipment stats
    public function getEquipmentStats() {
        $conn = $this->db->connexion();
        $query = "SELECT 
                  COUNT(*) as total,
                  SUM(CASE WHEN etat = 'disponible' THEN 1 ELSE 0 END) as available,
                  SUM(CASE WHEN etat = 'occupe' THEN 1 ELSE 0 END) as occupied,
                  SUM(CASE WHEN etat = 'maintenance' THEN 1 ELSE 0 END) as maintenance
                  FROM equipments";
        $result = $conn->query($query);
        $stats = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $stats;
    }

    // Get all equipment (alias for consistency)
    public function getAllEquipment() {
        return $this->getAllEquipments();
    }
}
?>
