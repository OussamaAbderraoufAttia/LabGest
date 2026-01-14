<?php
require_once("Model/dataBaseModel.php");

class userModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Authenticate user
    public function login($username, $password) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $user;
    }
    
    // Get user by ID
    public function getUserById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM users WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $user;
    }
    
    // Update profile
    public function updateProfile($userId, $data) {
        $conn = $this->db->connexion();
        $query = "UPDATE users SET nom = ?, prenom = ?, email = ?, domaine_recherche = ?, biographie = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $data['nom'], $data['prenom'], $data['email'], $data['domaine_recherche'], $data['biographie'], $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Update photo
    public function updatePhoto($userId, $photoPath) {
        $conn = $this->db->connexion();
        $query = "UPDATE users SET photo = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $photoPath, $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Update password
    public function updatePassword($userId, $newPassword) {
        $conn = $this->db->connexion();
        $query = "UPDATE users SET password = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $newPassword, $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    // Get user projects
    public function getUserProjects($userId) {
        $conn = $this->db->connexion();
        $query = "SELECT p.*, pm.role_projet FROM projects p
                  JOIN project_members pm ON p.id_project = pm.project_id
                  WHERE pm.user_id = ?
                  ORDER BY p.date_debut DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        $this->db->deconnexion($conn);
        return $projects;
    }
    
    // Get user publications
    public function getUserPublications($userId) {
        $conn = $this->db->connexion();
        $query = "SELECT pub.* FROM publications pub
                  JOIN publication_authors pa ON pub.id_pub = pa.pub_id
                  WHERE pa.user_id = ? AND pub.statut = 'valide'
                  ORDER BY pub.date_publication DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $publications = [];
        while ($row = $result->fetch_assoc()) {
            $publications[] = $row;
        }
        $this->db->deconnexion($conn);
        return $publications;
    }
    
    // Get all users (admin)
    public function getAllUsers($filters = []) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM users WHERE 1=1";
        
        if (!empty($filters['role'])) {
            $query .= " AND role = '" . $conn->real_escape_string($filters['role']) . "'";
        }
        
        if (!empty($filters['specialite'])) {
            $query .= " AND specialite LIKE '%" . $conn->real_escape_string($filters['specialite']) . "%'";
        }
        
        $query .= " ORDER BY nom ASC";
        $result = $conn->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $this->db->deconnexion($conn);
        return $users;
    }


    // Get total users count
    public function getTotalUsers() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM users";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }

    // Get users with filters
    public function getUsersWithFilters($role = null, $specialite = null, $search = null) {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];
        $types = "";

        if ($role) {
            $query .= " AND role = ?";
            $params[] = $role;
            $types .= "s";
        }

        if ($specialite) {
            $query .= " AND specialite = ?";
            $params[] = $specialite;
            $types .= "s";
        }

        if ($search) {
            $searchTerm = "%$search%";
            $query .= " AND (nom LIKE ? OR prenom LIKE ? OR username LIKE ? OR email LIKE ?)";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $types .= "ssss";
        }

        $query .= " ORDER BY nom, prenom";
        
        if (count($params) > 0) {
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($query);
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $this->db->deconnexion($conn);
        return $users;
    }

    // Get specialites
    public function getSpecialites() {
        $conn = $this->db->connexion();
        $query = "SELECT DISTINCT specialite FROM users WHERE specialite IS NOT NULL AND specialite != '' ORDER BY specialite";
        $result = $conn->query($query);
        $specialites = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['specialite']) {
                $specialites[] = $row['specialite'];
            }
        }
        $this->db->deconnexion($conn);
        return $specialites;
    }

    // Update user (Admin)
    public function updateUser($userId, $nom, $prenom, $email, $role, $grade, $specialite) {
        $conn = $this->db->connexion();
        $query = "UPDATE users SET nom = ?, prenom = ?, email = ?, role = ?, grade = ?, specialite = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $nom, $prenom, $email, $role, $grade, $specialite, $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Suspend user (Admin)
    public function suspendUser($userId) {
        $conn = $this->db->connexion();
        $query = "UPDATE users SET role = 'suspend' WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
}
?>
