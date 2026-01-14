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
    // Add Event
    // Get Event By ID
    public function getEventById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM events WHERE id_event = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result(); // Keep result open
        $event = $result->fetch_assoc();
        // $this->db->deconnexion($conn); // Keeping connection open for short script life
        return $event;
    }

    // Get Event Registrations (Users only - not guests)
    public function getEventRegistrations($eventId) {
        $conn = $this->db->connexion();
        $query = "SELECT er.id_registration, er.event_id, er.user_id, er.status, er.registration_date,
                         u.id_user, u.username, u.nom, u.prenom, u.email, u.photo, u.grade
                  FROM event_registrations er
                  LEFT JOIN users u ON er.user_id = u.id_user
                  WHERE er.event_id = ? AND er.user_id IS NOT NULL
                  ORDER BY er.registration_date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        $this->db->deconnexion($conn);
        return $registrations;
    }

    // Get Event Registration Count
    public function getRegistrationCount($eventId) {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ? AND user_id IS NOT NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'];
    }

    // Register User (Auth or Guest)
    public function registerUser($eventId, $userId, $guestData = null) {
        $conn = $this->db->connexion();
        if ($userId) {
            $query = "INSERT INTO event_registrations (event_id, user_id, status) VALUES (?, ?, 'confirmed')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $eventId, $userId);
        } else {
            $query = "INSERT INTO event_registrations (event_id, guest_name, guest_email, motivation, status) VALUES (?, ?, ?, ?, 'confirmed')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isss", $eventId, $guestData['name'], $guestData['email'], $guestData['motivation']);
        }
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Add Event (Updated)
    public function addEvent($titre, $description, $date, $lieu, $type, $image) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO events (titre, description, date_event, lieu, type, image_url, public_cible) VALUES (?, ?, ?, ?, ?, ?, 'public')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $titre, $description, $date, $lieu, $type, $image);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete Event
    public function deleteEvent($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM events WHERE id_event = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Add Offer
    public function addOffer($titre, $description, $date_limite, $type, $lien) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO offers (titre, description, date_limite, type, lien_postuler) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $titre, $description, $date_limite, $type, $lien);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete Offer
    public function deleteOffer($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM offers WHERE id_offer = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Get total events
    public function getTotalEvents() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM events";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }
}
?>
