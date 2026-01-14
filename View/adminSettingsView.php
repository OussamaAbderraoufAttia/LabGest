<?php
require_once("View/adminDashboardView.php");

class adminSettingsView extends adminDashboardView {
    
    public function afficherParametres($settings) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('settings'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Paramètres Généraux'); ?>
                    
                    <div class="admin-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto;">
                        <form method="POST" action="index.php?router=admin-settings" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div style="display: flex; gap: 30px; margin-bottom: 20px;">
                                <div style="flex: 1;">
                                    <div class="form-group">
                                        <label style="display:block; margin-bottom:5px; font-weight:600;">Nom du Laboratoire</label>
                                        <input type="text" name="nom_laboratoire" value="<?= htmlspecialchars($settings['nom_laboratoire']) ?>" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label style="display:block; margin-bottom:5px; font-weight:600;">Email de Contact</label>
                                        <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label style="display:block; margin-bottom:5px; font-weight:600;">Téléphone</label>
                                        <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label style="display:block; margin-bottom:5px; font-weight:600;">Couleur du Thème</label>
                                        <input type="color" name="theme_color" value="<?= htmlspecialchars($settings['theme_color']) ?>" style="width:100%; height:40px; border:1px solid #e2e8f0; border-radius:6px; padding:2px;">
                                    </div>
                                </div>
                                <div style="width: 200px; text-align: center;">
                                    <label style="display:block; margin-bottom:10px; font-weight:600;">Logo Actuel</label>
                                    <img src="<?= htmlspecialchars($settings['logo_url']) ?>" alt="Logo" style="width: 150px; height: 150px; object-fit: contain; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px;">
                                    <input type="file" name="logo" accept="image/*" style="width: 100%; font-size: 0.8rem;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">À propos du Laboratoire</label>
                                <textarea name="about_labo" rows="5" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px; resize: vertical;"><?= htmlspecialchars($settings['about_labo']) ?></textarea>
                            </div>
                            
                            <div style="text-align: right; margin-top: 20px;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 25px; font-size: 1rem;">
                                    <i class="fa-solid fa-save"></i> Enregistrer les Modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </body>
        </html>
        <?php
    }
}
?>
