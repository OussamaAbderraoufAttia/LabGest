<?php
require_once("dataBaseModel.php");

class teamModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all teams
    // Get all teams with stats
    // Get all teams with stats (members count and publications count)
    public function getAllTeams() {
        $conn = $this->db->connexion();
        // Subqueries are safer for aggregation to avoid cartesian product issues if joining multiple one-to-many tables
        $query = "SELECT t.*, 
                         u.nom as chef_nom, 
                         u.prenom as chef_prenom,
                         (SELECT COUNT(*) FROM team_members tm WHERE tm.team_id = t.id_team) as member_count,
                         (SELECT COUNT(DISTINCT pa.pub_id) 
                          FROM team_members tm 
                          JOIN publication_authors pa ON tm.user_id = pa.user_id 
                          WHERE tm.team_id = t.id_team) as pub_count
                  FROM teams t
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  ORDER BY t.nom";
        $result = $conn->query($query);
        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row;
        }
        $this->db->deconnexion($conn);
        return $teams;
    }
    
    // Get team members
    public function getTeamMembers($teamId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.*, tm.role_dans_equipe
                  FROM users u
                  JOIN team_members tm ON u.id_user = tm.user_id
                  WHERE tm.team_id = ?
                  ORDER BY u.nom";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
        $this->db->deconnexion($conn);
        return $members;
    }
    
    // Get team publications (from all members)
    public function getTeamPublications($teamId) {
        $conn = $this->db->connexion();
        $query = "SELECT DISTINCT pub.* 
                  FROM publications pub
                  JOIN publication_authors pa ON pub.id_pub = pa.pub_id
                  JOIN team_members tm ON pa.user_id = tm.user_id
                  WHERE tm.team_id = ? AND pub.statut = 'valide'
                  ORDER BY pub.date_publication DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $publications = [];
        while ($row = $result->fetch_assoc()) {
            $publications[] = $row;
        }
        $this->db->deconnexion($conn);
        return $publications;
    }
    
    // Get laboratory director
    public function getDirector() {
        $conn = $this->db->connexion();
        // Fetch director ID from settings table, or fallback if settings empty
        $query = "SELECT u.* 
                  FROM users u 
                  JOIN settings s ON s.directeur_labo_id = u.id_user 
                  LIMIT 1";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $director = $result->fetch_assoc();
        } else {
            // Fallback: Find a professor or the admin if no settings
            $queryFallback = "SELECT * FROM users WHERE grade LIKE '%Prof%' LIMIT 1";
            $resFallback = $conn->query($queryFallback);
            if ($resFallback && $resFallback->num_rows > 0) {
                 $director = $resFallback->fetch_assoc();
            } else {
                 // Last resort
                 $director = ['nom' => 'Directeur', 'prenom' => 'Non Défini', 'grade' => 'N/A', 'photo' => 'View/assets/default_avatar.png'];
            }
        }
        
        $this->db->deconnexion($conn);
        return $director;
    }
    // Get specific team details
    public function getTeam($id) {
        $conn = $this->db->connexion();
        $query = "SELECT t.*, u.nom as chef_nom, u.prenom as chef_prenom, u.photo as chef_photo, u.grade as chef_grade, u.email as chef_email
                  FROM teams t
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE t.id_team = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $team = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $team;
    }
    // Add Team
    public function addTeam($nom, $description, $chef_id, $photo) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO teams (nom, description, chef_id, photo) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssis", $nom, $description, $chef_id, $photo);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Update Team
    public function updateTeam($id, $nom, $description, $chef_id, $photo) {
        $conn = $this->db->connexion();
        $query = "UPDATE teams SET nom=?, description=?, chef_id=?, photo=? WHERE id_team=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisi", $nom, $description, $chef_id, $photo, $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete Team
    public function deleteTeam($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM teams WHERE id_team = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Get total teams
    public function getTotalTeams() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM teams";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }

    // Add member to team
    public function addTeamMember($teamId, $userId, $roleInTeam = 'Membre') {
        $conn = $this->db->connexion();
        // Check if already a member
        $checkQuery = "SELECT * FROM team_members WHERE team_id = ? AND user_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $teamId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $this->db->deconnexion($conn);
            return false; // Already a member
        }
        
        $query = "INSERT INTO team_members (team_id, user_id, role_dans_equipe) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $teamId, $userId, $roleInTeam);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Remove member from team
    public function removeTeamMember($teamId, $userId) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM team_members WHERE team_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $teamId, $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Check if user is team chef
    public function isTeamChef($teamId, $userId) {
        $conn = $this->db->connexion();
        $query = "SELECT chef_id FROM teams WHERE id_team = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $team = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $team && $team['chef_id'] == $userId;
    }

    // Check if user is team member
    public function isTeamMember($teamId, $userId) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM team_members WHERE team_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $teamId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $isMember = $result->num_rows > 0;
        $this->db->deconnexion($conn);
        return $isMember;
    }

    // Get available users to add (not yet in team)
    public function getAvailableUsersForTeam($teamId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.* FROM users u 
                  WHERE u.id_user NOT IN (
                      SELECT user_id FROM team_members WHERE team_id = ?
                  ) AND u.id_user NOT IN (
                      SELECT chef_id FROM teams WHERE id_team = ?
                  )
                  ORDER BY u.nom, u.prenom";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $teamId, $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $this->db->deconnexion($conn);
        return $users;
    }
}
?>
