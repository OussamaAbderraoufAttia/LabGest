<?php
require_once("dataBaseModel.php");

class publicationModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all validated publications with filters
    public function getAllPublications($filters = []) {
        $conn = $this->db->connexion();
        $query = "SELECT pub.* FROM publications pub WHERE pub.statut = 'valide'";
        
        if (!empty($filters['year'])) {
            $query .= " AND YEAR(pub.date_publication) = " . intval($filters['year']);
        }
        
        if (!empty($filters['type'])) {
            $query .= " AND pub.type = '" . $conn->real_escape_string($filters['type']) . "'";
        }
        
        if (!empty($filters['search'])) {
            $search = $conn->real_escape_string($filters['search']);
            $query .= " AND (pub.titre LIKE '%$search%' OR pub.resume LIKE '%$search%')";
        }
        
        $query .= " ORDER BY pub.date_publication DESC";
        $result = $conn->query($query);
        $publications = [];
        while ($row = $result->fetch_assoc()) {
            $publications[] = $row;
        }
        $this->db->deconnexion($conn);
        return $publications;
    }
    
    // Get publication authors
    public function getAuthors($pubId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.* FROM users u
                  JOIN publication_authors pa ON u.id_user = pa.user_id
                  WHERE pa.pub_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $pubId);
        $stmt->execute();
        $result = $stmt->get_result();
        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }
        $this->db->deconnexion($conn);
        return $authors;
    }
    
    // Get available years
    public function getYears() {
        $conn = $this->db->connexion();
        $query = "SELECT DISTINCT YEAR(date_publication) as year FROM publications WHERE statut = 'valide' ORDER BY year DESC";
        $result = $conn->query($query);
        $years = [];
        while ($row = $result->fetch_row()) {
            $years[] = $row[0];
        }
        $this->db->deconnexion($conn);
        return $years;
    }
    
    // Get available types
    public function getTypes() {
        $conn = $this->db->connexion();
        $query = "SELECT DISTINCT type FROM publications WHERE statut = 'valide' AND type IS NOT NULL ORDER BY type";
        $result = $conn->query($query);
        $types = [];
        while ($row = $result->fetch_row()) {
            $types[] = $row[0];
        }
        $this->db->deconnexion($conn);
        return $types;
    }
}
?>
