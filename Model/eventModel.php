<?php
require_once("dataBaseModel.php");

class eventModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get upcoming events
    public function getUpcomingEvents() {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM events WHERE date_event >= NOW() ORDER BY date_event ASC";
        $result = $conn->query($query);
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $this->db->deconnexion($conn);
        return $events;
    }
    
    // Get all events
    public function getAllEvents() {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM events ORDER BY date_event DESC";
        $result = $conn->query($query);
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $this->db->deconnexion($conn);
        return $events;
    }
    
    // Get opportunities/offers
    public function getOffers() {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM offers WHERE date_limite >= NOW() ORDER BY date_limite ASC";
        $result = $conn->query($query);
        $offers = [];
        while ($row = $result->fetch_assoc()) {
            $offers[] = $row;
        }
        $this->db->deconnexion($conn);
        return $offers;
    }
}
?>
