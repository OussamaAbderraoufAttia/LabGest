<?php
require_once("View/loginView.php");
require_once("View/userProfileView.php");
require_once("Model/userModel.php");

class userController {
    
    public function afficherPageLogin() {
        $view = new loginView();
        $view->afficherPage();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $model = new userModel();
            $user = $model->login($username, $password);
            
            if ($user) {
                // Start session if not already started
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Store user data based on role - FIXED: Check 'role' field, not username
                if ($user['role'] === 'admin') {
                    $_SESSION['admin'] = $user;
                    header("Location: index.php?router=admin-dashboard");
                } else {
                    $_SESSION['user'] = $user;
                    header("Location: index.php?router=profil");
                }
                exit();
            } else {
                // Login failed
                header("Location: index.php?router=login&error=1");
                exit();
            }
        }
    }
    
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Clear all session variables
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        header("Location: index.php?router=accueil");
        exit();
    }
    
    public function afficherProfil() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        
        if (!$userId) {
            header("Location: index.php?router=login");
            exit();
        }
        
        $model = new userModel();
        $user = $model->getUserById($userId);
        $projects = $model->getUserProjects($userId);
        $publications = $model->getUserPublications($userId);
        
        $view = new userProfileView();
        $view->afficherProfil($user, $projects, $publications);
    }
    
    public function modifyPersoInfo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
            
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Non autorisé']);
                exit();
            }
            
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'prenom' => $_POST['prenom'] ?? '',
                'email' => $_POST['email'] ?? '',
                'domaine_recherche' => $_POST['domaine_recherche'] ?? '',
                'biographie' => $_POST['biographie'] ?? ''
            ];
            
            $model = new userModel();
            $result = $model->updateProfile($userId, $data);
            
            if ($result) {
                // Update session data
                $user = $model->getUserById($userId);
                if (isset($_SESSION['admin'])) {
                    $_SESSION['admin'] = $user;
                } else {
                    $_SESSION['user'] = $user;
                }
                header("Location: index.php?router=profil&success=1");
            } else {
                header("Location: index.php?router=profil&error=1");
            }
            exit();
        }
    }
    
    public function modifyPdp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
            
            if (!$userId) {
                echo json_encode(['success' => false]);
                exit();
            }
            
            $file = $_FILES['photo'];
            $uploadDir = 'uploads/photos/';
            $fileName = $userId . '_' . time() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $model = new userModel();
                $model->updatePhoto($userId, $targetPath);
                
                // Update session
                $user = $model->getUserById($userId);
                if (isset($_SESSION['admin'])) {
                    $_SESSION['admin'] = $user;
                } else {
                    $_SESSION['user'] = $user;
                }
                
                header("Location: index.php?router=profil&photo_success=1");
            } else {
                header("Location: index.php?router=profil&photo_error=1");
            }
            exit();
        }
    }
    
    public function modifyPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $userId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            
            if (!$userId) {
                header("Location: index.php?router=login");
                exit();
            }
            
            $model = new userModel();
            $user = $model->getUserById($userId);
            
            if ($user['password'] === $currentPassword) {
                $model->updatePassword($userId, $newPassword);
                header("Location: index.php?router=profil&password_success=1");
            } else {
                header("Location: index.php?router=profil&password_error=1");
            }
            exit();
        }
    }
}
?>
