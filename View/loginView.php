<?php
require_once("commonViews.php");

class loginView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Connexion - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/loginStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        </head>
        <?php
    }
    
    public function afficherPage() {
        $common = new commonViews();
        $error = isset($_GET['error']) ? true : false;
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body class="login-page">
                <?php $common->navBarD(); ?>
                <div class="login-container">
                    <div class="login-box">
                        <div class="login-header">
                            <img src="View/assets/logo.png" alt="LRE Logo" width="100px">
                            <h1>Connexion</h1>
                            <p>Accédez à votre espace personnel</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Identifiants incorrects. Veuillez réessayer.
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?router=process-login" class="login-form">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Entrez votre nom d'utilisateur" 
                                           required
                                           autocomplete="username">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Entrez votre mot de passe" 
                                           required
                                           autocomplete="current-password">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-login-submit">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                Se Connecter
                            </button>
                        </form>
                        
                        <div class="login-info">
                            <p><strong>Comptes de test :</strong></p>
                            <p>Admin : <code>admin</code> / <code>admin</code></p>
                            <p>Utilisateur : <code>user</code> / <code>user</code></p>
                        </div>
                    </div>
                </div>
            </body>
        </html>
        <?php
    }
}
?>
