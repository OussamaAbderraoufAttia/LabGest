<?php
require_once("dataBaseModel.php");

class settingsModel {
    private $db;
    
    public function __construct() {
        $this->db = new dataBaseModel();
    }
    
    public function getSettings() {
        $conn = $this->db->connexion();
        $query = "SELECT * FROM settings WHERE id = 1";
        $result = $conn->query($query);
        $settings = $result->fetch_assoc();
        $this->db->deconnexion($conn);
        return $settings;
    }
    
    public function updateSettings($nom, $about, $email, $phone, $color, $logo = null) {
        $conn = $this->db->connexion();
        if ($logo) {
            $query = "UPDATE settings SET nom_laboratoire=?, about_labo=?, contact_email=?, contact_phone=?, theme_color=?, logo_url=? WHERE id=1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $nom, $about, $email, $phone, $color, $logo);
        } else {
            $query = "UPDATE settings SET nom_laboratoire=?, about_labo=?, contact_email=?, contact_phone=?, theme_color=? WHERE id=1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $nom, $about, $email, $phone, $color);
        }
        $result = $stmt->execute();
        $this->db->deconnexion($conn);
        return $result;
    }
    
    public function backupDatabase() {
        // Simple PHP Backup Logic
        // In real world, use mysqldump, but here use strict PHP for compatibility
        $conn = $this->db->connexion();
        
        $tables = [];
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        $sqlScript = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSTART TRANSACTION;\nSET time_zone = \"+00:00\";\n\n";
        
        foreach ($tables as $table) {
            $result = $conn->query("SELECT * FROM $table");
            $num_fields = $result->field_count;
            
            $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
            $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
            $sqlScript .= "\n\n" . $row2[1] . ";\n\n";
            
            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = $result->fetch_row()) {
                    $sqlScript .= "INSERT INTO `$table` VALUES(";
                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $sqlScript .= '"' . $row[$j] . '"';
                        } else {
                            $sqlScript .= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }
            $sqlScript .= "\n\n\n";
        }
        
        $sqlScript .= "COMMIT;";
        
        $backup_name = "backup_tdw_" . date("Y-m-d_H-i-s") . ".sql";
        $backup_path = "uploads/" . $backup_name;
        file_put_contents($backup_path, $sqlScript);
        
        $this->db->deconnexion($conn);
        return $backup_path;
    }
}
?>
