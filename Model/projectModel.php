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
    // Add new project (no image_url in tdw_enriched schema)
    public function addProject($titre, $description, $domaine, $date_debut, $date_fin, $statut, $responsable_id) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO projects (titre, description, thematique, date_debut, date_fin, statut, responsable_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $titre, $description, $domaine, $date_debut, $date_fin, $statut, $responsable_id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Update existing project (no image_url in tdw_enriched schema)
    public function updateProject($id, $titre, $description, $domaine, $date_debut, $date_fin, $statut, $responsable_id) {
        $conn = $this->db->connexion();
        $query = "UPDATE projects SET titre=?, description=?, thematique=?, date_debut=?, date_fin=?, statut=?, responsable_id=? WHERE id_project=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssii", $titre, $description, $domaine, $date_debut, $date_fin, $statut, $responsable_id, $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete project
    public function deleteProject($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM projects WHERE id_project = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // ============ PERMISSION & MEMBERSHIP METHODS ============

    // Check if user is project responsable (leader)
    public function isResponsable($projectId, $userId) {
        $conn = $this->db->connexion();
        $query = "SELECT responsable_id FROM projects WHERE id_project = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $project && $project['responsable_id'] == $userId;
    }

    // Check if user is project member
    public function isProjectMember($projectId, $userId) {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM project_members WHERE project_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $projectId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] > 0;
    }

    // Get project members with details
    public function getProjectMembersWithDetails($projectId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.id_user, u.nom, u.prenom, u.email, u.photo, u.role, pm.role_projet
                  FROM users u
                  JOIN project_members pm ON u.id_user = pm.user_id
                  WHERE pm.project_id = ?
                  ORDER BY u.nom";
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

    // Add member to project
    public function addProjectMember($projectId, $userId, $roleProjet = 'Membre') {
        // Check if invite role - not allowed
        $conn = $this->db->connexion();
        $userQuery = "SELECT role FROM users WHERE id_user = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();
        
        if ($user['role'] === 'invite') {
            $this->db->deconnexion($conn);
            return false; // Invite users cannot be added
        }

        // Check if already member
        $checkQuery = "SELECT id FROM project_members WHERE project_id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $projectId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $this->db->deconnexion($conn);
            return false; // Already member
        }

        // Add member
        $query = "INSERT INTO project_members (project_id, user_id, role_projet) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $projectId, $userId, $roleProjet);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Remove member from project
    public function removeProjectMember($projectId, $userId) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM project_members WHERE project_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $projectId, $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Close project (change status to 'termine')
    public function closeProject($projectId) {
        $conn = $this->db->connexion();
        $status = 'termine';
        $query = "UPDATE projects SET statut = ? WHERE id_project = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $projectId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Get eligible users for project (exclude 'invite' role)
    public function getEligibleMembers() {
        $conn = $this->db->connexion();
        $query = "SELECT id_user, nom, prenom, email, role FROM users WHERE role != 'invite' ORDER BY nom";
        $result = $conn->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $this->db->deconnexion($conn);
        return $users;
    }

    // Get total projects count
    public function getTotalProjects() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM projects";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }
}
?>
