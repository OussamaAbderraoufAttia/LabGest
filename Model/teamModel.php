<?php
require_once("dataBaseModel.php");

class teamModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all teams
    public function getAllTeams() {
        $conn = $this->db->connexion();
        $query = "SELECT t.*, u.nom as chef_nom, u.prenom as chef_prenom
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
        $query = "SELECT u.* FROM users u WHERE u.role = 'admin' LIMIT 1";
        $result = $conn->query($query);
        $director = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $director;
    }
}
?>
