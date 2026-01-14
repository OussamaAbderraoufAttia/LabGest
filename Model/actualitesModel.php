<?php
require_once("dataBaseModel.php");

class actualitesModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    // Get all published actualites
    public function getAllActualites() {
        $conn = $this->db->connexion();
        $query = "SELECT a.*, u.prenom, u.nom FROM actualites a
                  LEFT JOIN users u ON a.auteur_id = u.id_user
                  WHERE a.statut = 'publiee'
                  ORDER BY a.date_publication DESC";
        $result = $conn->query($query);
        $actualites = [];
        while ($row = $result->fetch_assoc()) {
            $actualites[] = $row;
        }
        $this->db->deconnexion($conn);
        return $actualites;
    }

    // Get actualites with limit (for admin panel)
    public function getActualitesWithLimit($limit = 5, $offset = 0) {
        $conn = $this->db->connexion();
        $query = "SELECT a.*, u.prenom, u.nom FROM actualites a
                  LEFT JOIN users u ON a.auteur_id = u.id_user
                  WHERE a.statut = 'publiee'
                  ORDER BY a.date_publication DESC
                  LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $actualites = [];
        while ($row = $result->fetch_assoc()) {
            $actualites[] = $row;
        }
        $this->db->deconnexion($conn);
        return $actualites;
    }

    // Get total count of published actualites
    public function getTotalActualites() {
        $conn = $this->db->connexion();
        $query = "SELECT COUNT(*) as count FROM actualites WHERE statut = 'publiee'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $row['count'];
    }

    // Get actualite by ID
    public function getActualiteById($id) {
        $conn = $this->db->connexion();
        $query = "SELECT a.*, u.prenom, u.nom FROM actualites a
                  LEFT JOIN users u ON a.auteur_id = u.id_user
                  WHERE a.id_actualite = ? AND a.statut = 'publiee'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $actualite = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $actualite;
    }

    // Add new actualite (admin only)
    public function addActualite($titre, $description, $contenu_complet, $categorie, $image_url, $auteur_id) {
        $conn = $this->db->connexion();
        $statut = 'publiee';
        $query = "INSERT INTO actualites (titre, description, contenu_complet, categorie, image_url, auteur_id, statut, date_publication)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $titre, $description, $contenu_complet, $categorie, $image_url, $auteur_id, $statut);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Update actualite
    public function updateActualite($id, $titre, $description, $contenu_complet, $categorie, $image_url) {
        $conn = $this->db->connexion();
        $query = "UPDATE actualites SET titre = ?, description = ?, contenu_complet = ?, categorie = ?, image_url = ?, date_modification = NOW()
                  WHERE id_actualite = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $titre, $description, $contenu_complet, $categorie, $image_url, $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Delete actualite
    public function deleteActualite($id) {
        $conn = $this->db->connexion();
        $query = "DELETE FROM actualites WHERE id_actualite = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }

    // Archive actualite (change status to archivee)
    public function archiveActualite($id) {
        $conn = $this->db->connexion();
        $statut = 'archivee';
        $query = "UPDATE actualites SET statut = ? WHERE id_actualite = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
}
?>
