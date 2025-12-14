<?php
// Fichier : dataBaseModel.php
// Classe de connexion à la base de données TDW

class dataBaseModel {
    private $host = "localhost";
    private $dbname = "TDW";
    private $username = "root";
    private $password = "";
    
    public function connexion() {
        $connexion = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        
        if ($connexion->connect_error) {
            die("Échec de la connexion à la BDD : " . $connexion->connect_error);
        }
        
        $connexion->set_charset("utf8");
        
        // Initialisation des sessions pour l'authentification
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $connexion;
    }
    
    public function deconnexion($connexion) {
        $connexion->close();
    }
    
    public function query($connexion, $query, $params = []) {
        // Prepare statement
        $stmt = $connexion->prepare($query);
        
        if (!$stmt) {
            die("Erreur de préparation : " . $connexion->error);
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            $types = '';
            $values = [];
            
            foreach ($params as $key => $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_double($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $value;
            }
            
            $stmt->bind_param($types, ...$values);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>
