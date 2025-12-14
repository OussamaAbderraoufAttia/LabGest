<?php
require_once("dataBaseModel.php");

class projectModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all projects with optional filters
    public function getAllProjects($filters = []) {
        $conn = $this->db->connexion();
        $query = "SELECT p.*, u.nom AS responsable_nom, u.prenom AS responsable_prenom 
                  FROM projects p
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE 1=1";
        
        if (!empty($filters['thematique'])) {
            $query .= " AND p.thematique = '" . $conn->real_escape_string($filters['thematique']) . "'";
        }
        
        if (!empty($filters['statut'])) {
            $query .= " AND p.statut = '" . $conn->real_escape_string($filters['statut']) . "'";
        }
        
        $query .= " ORDER BY p.date_debut DESC";
        $result = $conn->query($query);
        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        $this->db->deconnexion($conn);
        return $projects;
    }
    
    // Get project by ID with all details
    public function getProjectById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT p.*, u.nom AS responsable_nom, u.prenom AS responsable_prenom, u.email AS responsable_email
                  FROM projects p
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE p.id_project = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $project;
    }
    
    // Get project members
    public function getProjectMembers($projectId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.*, pm.role_projet FROM users u
                  JOIN project_members pm ON u.id_user = pm.user_id
                  WHERE pm.project_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
        $this->db->deconnexion($conn);
        return $members;
    }
    
    // Get project publications
    public function getProjectPublications($projectId) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM publications WHERE project_id = ? AND statut = 'valide' ORDER BY date_publication DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $publications = [];
        while ($row = $result->fetch_assoc()) {
            $publications[] = $row;
        }
        $this->db->deconnexion($conn);
        return $publications;
    }
    
    // Get project partners
    public function getProjectPartners($projectId) {
        $conn = $this->db->connexion();
        $query = "SELECT partners.* FROM partners
                  JOIN project_partners pp ON partners.id_partner = pp.partner_id
                  WHERE pp.project_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = $row;
        }
        $this->db->deconnexion($conn);
        return $partners;
    }
    
    // Get available themes
    public function getThemes() {
        $conn = $this->db->connexion();
        $query = "SELECT DISTINCT thematique FROM projects WHERE thematique IS NOT NULL ORDER BY thematique";
        $result = $conn->query($query);
        $themes = [];
        while ($row = $result->fetch_row()) {
            $themes[] = $row[0];
        }
        $this->db->deconnexion($conn);
        return $themes;
    }
}
?>
