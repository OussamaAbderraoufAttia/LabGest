<?php
require_once("commonViews.php");
require_once("Model/userModel.php");

class userProfileView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mon Profil - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/profileStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        </head>
        <?php
    }
    
    public function afficherProfil($user, $projects, $publications) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-photo">
                            <img src="<?= !empty($user['photo']) ? $user['photo'] : 'View/assets/default_avatar.png' ?>" alt="Photo de profil">
                            <form method="POST" action="index.php?router=update-photo" enctype="multipart/form-data" id="photoForm">
                                <label for="photoInput" class="photo-upload-btn">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;" onchange="document.getElementById('photoForm').submit();">
                            </form>
                        </div>
                        <div class="profile-info">
                            <h1><?= $user['prenom'] ?> <?= $user['nom'] ?></h1>
                            <p class="role"><?= ucfirst($user['role']) ?> - <?= $user['grade'] ?></p>
                            <p class="specialite"><i class="fa-solid fa-graduation-cap"></i> <?= $user['specialite'] ?></p>
                            <p class="email"><i class="fa-solid fa-envelope"></i> <?= $user['email'] ?></p>
                        </div>
                    </div>
                    
                    <div class="profile-tabs">
                        <button class="tab-btn active" onclick="showTab('info')">Informations</button>
                        <button class="tab-btn" onclick="showTab('projects')">Projets (<?= count($projects) ?>)</button>
                        <button class="tab-btn" onclick="showTab('publications')">Publications (<?= count($publications) ?>)</button>
                        <button class="tab-btn" onclick="showTab('security')">Sécurité</button>
                    </div>
                    
                    <!-- Info Tab -->
                    <div id="info-tab" class="tab-content active">
                        <form method="POST" action="index.php?router=update-profile">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nom</label>
                                    <input type="text" name="nom" value="<?= $user['nom'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Prénom</label>
                                    <input type="text" name="prenom" value="<?= $user['prenom'] ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?= $user['email'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Domaine de recherche</label>
                                <input type="text" name="domaine_recherche" value="<?= $user['domaine_recherche'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Biographie</label>
                                <textarea name="biographie" rows="5"><?= $user['biographie'] ?></textarea>
                            </div>
                            <button type="submit" class="btn-primary">Sauvegarder</button>
                        </form>
                    </div>
                    
                    <!-- Projects Tab -->
                    <div id="projects-tab" class="tab-content">
                        <div class="projects-grid">
                            <?php if (empty($projects)): ?>
                                <p>Aucun projet pour le moment.</p>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <div class="project-card">
                                        <h3><?= $project['titre'] ?></h3>
                                        <p><?= $project['description'] ?></p>
                                        <span class="badge"><?= $project['role_projet'] ?></span>
                                        <span class="badge status-<?= $project['statut'] ?>"><?= ucfirst($project['statut']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Publications Tab -->
                    <div id="publications-tab" class="tab-content">
                        <div class="publications-list">
                            <?php if (empty($publications)): ?>
                                <p>Aucune publication pour le moment.</p>
                            <?php else: ?>
                                <?php foreach ($publications as $pub): ?>
                                    <div class="publication-item">
                                        <h4><?= $pub['titre'] ?></h4>
                                        <p class="pub-date"><?= date('Y', strtotime($pub['date_publication'])) ?> - <?= $pub['type'] ?></p>
                                        <?php if ($pub['resume']): ?>
                                            <p><?= substr($pub['resume'], 0, 200) ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Security Tab -->
                    <div id="security-tab" class="tab-content">
                        <h3>Modification du mot de passe</h3>
                        <form method="POST" action="index.php?router=update-password">
                            <div class="form-group">
                                <label>Mot de passe actuel</label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label>Nouveau mot de passe</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmer le nouveau mot de passe</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn-primary">Changer le mot de passe</button>
                        </form>
                    </div>
                </div>
                
                <script>
                    function showTab(tabName) {
                        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                        
                        document.getElementById(tabName + '-tab').classList.add('active');
                        event.target.classList.add('active');
                    }
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
