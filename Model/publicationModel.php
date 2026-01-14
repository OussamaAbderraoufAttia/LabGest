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
        $query = "SELECT DISTINCT pub.* FROM publications pub ";
        
        // Joins for filters
        if (!empty($filters['team_id'])) {
            $query .= " JOIN publication_authors pa ON pub.id_pub = pa.pub_id 
                        JOIN team_members tm ON pa.user_id = tm.user_id ";
        }
        
        $query .= " WHERE pub.statut = 'valide'";
        
        if (!empty($filters['year'])) {
            $query .= " AND YEAR(pub.date_publication) = " . intval($filters['year']);
        }
        
        if (!empty($filters['type'])) {
            $query .= " AND pub.type = '" . $conn->real_escape_string($filters['type']) . "'";
        }
        
        if (!empty($filters['project_id'])) {
             $query .= " AND pub.project_id = " . intval($filters['project_id']);
        }
        
        if (!empty($filters['team_id'])) {
             $query .= " AND tm.team_id = " . intval($filters['team_id']);
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
    
    // Alias for controller compatibility
    public function getPublicationAuthors($pubId) {
        return $this->getAuthors($pubId);
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
    // Get Pending Publications (for validation)
    public function getPendingPublications() {
        $conn = $this->db->connexion();
        $query = "SELECT pub.*, u.nom as author_nom, u.prenom as author_prenom 
                  FROM publications pub
                  LEFT JOIN publication_authors pa ON pub.id_pub = pa.pub_id
                  LEFT JOIN users u ON pa.user_id = u.id_user
                  WHERE pub.statut = 'soumis' OR pub.statut = 'en attente'
                  GROUP BY pub.id_pub ORDER BY pub.date_publication DESC";
        $result = $conn->query($query);
        $publications = [];
        while ($row = $result->fetch_assoc()) {
            $publications[] = $row;
        }
        $this->db->deconnexion($conn);
        return $publications;
    }

    // Validate Publication
    public function validatePublication($id) {
        $conn = $this->db->connexion();
        $query = "UPDATE publications SET statut = 'valide' WHERE id_pub = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Add Publication (Admin)
    public function addPublication($titre, $resume, $date, $type, $lien, $project_id = null) {
        $conn = $this->db->connexion();
        $query = "INSERT INTO publications (titre, resume, date_publication, type, doi, project_id, statut) 
                  VALUES (?, ?, ?, ?, ?, ?, 'valide')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $titre, $resume, $date, $type, $lien, $project_id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Add publication with multiple authors
    public function addPublicationWithAuthors($data, $authorIds = []) {
        $conn = $this->db->connexion();
        
        // Insert publication
        $query = "INSERT INTO publications (titre, resume, type, date_publication, doi, project_id, team_id, statut) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'valide')";
        $stmt = $conn->prepare($query);
        $titre = $data['titre'] ?? '';
        $resume = $data['resume'] ?? '';
        $type = $data['type'] ?? 'article';
        $date = $data['date_publication'] ?? date('Y-m-d');
        $doi = $data['doi'] ?? null;
        $projectId = $data['project_id'] ?? null;
        $teamId = $data['team_id'] ?? null;
        
        $stmt->bind_param("sssssii", $titre, $resume, $type, $date, $doi, $projectId, $teamId);
        
        if (!$stmt->execute()) {
            $this->db->deconnexion($conn);
            return false;
        }
        
        $pubId = $conn->insert_id;
        
        // Add authors
        if (!empty($authorIds)) {
            $authorQuery = "INSERT INTO publication_authors (pub_id, user_id, ordre_auteur) VALUES (?, ?, ?)";
            $authorStmt = $conn->prepare($authorQuery);
            
            foreach ($authorIds as $ordre => $userId) {
                $authorOrder = $ordre + 1;
                $authorStmt->bind_param("iii", $pubId, $userId, $authorOrder);
                $authorStmt->execute();
            }
        }
        
        $this->db->deconnexion($conn);
        return $pubId;
    }

    // Get project members for author selection
    public function getProjectMembersForAuthors($projectId) {
        $conn = $this->db->connexion();
        $query = "SELECT u.id_user, u.nom, u.prenom, u.email 
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
    
    // Delete Publication
    public function deletePublication($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM publications WHERE id_pub = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Get total publications
    public function getTotalPublications() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM publications WHERE statut = 'valide'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'] ?? 0;
    }
}
?>
