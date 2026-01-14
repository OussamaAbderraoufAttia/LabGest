<?php
require_once("commonViews.php");

class adminView {
    
    public function afficher_dashboard($stats) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Tableau de Bord Admin</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .admin-container {
                    max-width: 1400px;
                    margin: 100px auto 40px;
                    padding: 20px;
                }
                .admin-header {
                    margin-bottom: 40px;
                }
                .admin-header h1 {
                    font-size: 2.5em;
                    color: #333;
                    margin-bottom: 10px;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    margin-bottom: 40px;
                }
                .stat-card {
                    background: white;
                    padding: 25px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-align: center;
                    border-left: 4px solid #667eea;
                }
                .stat-card i {
                    font-size: 2.5em;
                    color: #667eea;
                    margin-bottom: 10px;
                }
                .stat-card h3 {
                    margin: 0;
                    color: #666;
                    font-size: 0.9em;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .stat-card .number {
                    font-size: 2em;
                    color: #333;
                    font-weight: bold;
                    margin-top: 10px;
                }
                .categories-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                }
                .category-card {
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
                    transition: transform 0.3s, box-shadow 0.3s;
                    cursor: pointer;
                }
                .category-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
                }
                .category-header {
                    padding: 20px;
                    color: white;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .category-header i {
                    font-size: 2em;
                }
                .category-header h3 {
                    margin: 0;
                    font-size: 1.3em;
                }
                .category-body {
                    padding: 20px;
                }
                .category-body p {
                    color: #666;
                    margin: 0 0 15px 0;
                    font-size: 0.95em;
                    line-height: 1.5;
                }
                .category-actions {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }
                .btn-action {
                    padding: 8px 15px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-size: 0.85em;
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s;
                }
                .btn-primary-admin {
                    background-color: #667eea;
                    color: white;
                }
                .btn-primary-admin:hover {
                    background-color: #764ba2;
                }
                .btn-secondary {
                    background-color: #e9ecef;
                    color: #333;
                }
                .btn-secondary:hover {
                    background-color: #dee2e6;
                }
                .cat-users { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
                .cat-teams { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
                .cat-projects { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
                .cat-equipment { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
                .cat-publications { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
                .cat-events { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
                .cat-settings { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="admin-container">
                <div class="admin-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord Administrateur</h1>
                    <p style="color: #666;">Bienvenue dans l'interface d'administration du laboratoire</p>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3>Utilisateurs</h3>
                        <div class="number"><?php echo $stats['total_users']; ?></div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-sitemap"></i>
                        <h3>Équipes</h3>
                        <div class="number"><?php echo $stats['total_teams'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-project-diagram"></i>
                        <h3>Projets</h3>
                        <div class="number"><?php echo $stats['total_projects']; ?></div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-tools"></i>
                        <h3>Équipements</h3>
                        <div class="number"><?php echo $stats['total_equipment']; ?></div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>Publications</h3>
                        <div class="number"><?php echo $stats['total_publications']; ?></div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Événements</h3>
                        <div class="number"><?php echo $stats['total_events']; ?></div>
                    </div>
                </div>

                <!-- Categories -->
                <h2 style="margin-bottom: 30px;">Gestion & Administration</h2>
                <div class="categories-grid">
                    <!-- 1. Users -->
                    <div class="category-card">
                        <div class="category-header cat-users">
                            <i class="fas fa-users-cog"></i>
                            <h3>Gestion des Utilisateurs</h3>
                        </div>
                        <div class="category-body">
                            <p>Gérez les utilisateurs, assignez les rôles et permissions. Ajoutez, modifiez ou supprimez des comptes.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_users" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Voir tous
                                </a>
                                <a href="index.php?router=admin_users_add" class="btn-action btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Teams -->
                    <div class="category-card">
                        <div class="category-header cat-teams">
                            <i class="fas fa-sitemap"></i>
                            <h3>Gestion des Équipes</h3>
                        </div>
                        <div class="category-body">
                            <p>Organisez les équipes de recherche. Attribuez les chefs d'équipe et gérez les membres.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_teams" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Voir tous
                                </a>
                                <a href="index.php?router=admin_teams_add" class="btn-action btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Projects -->
                    <div class="category-card">
                        <div class="category-header cat-projects">
                            <i class="fas fa-project-diagram"></i>
                            <h3>Gestion des Projets</h3>
                        </div>
                        <div class="category-body">
                            <p>Créez et gérez les projets de recherche. Associez les membres et suivez les statistiques.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_projects" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Voir tous
                                </a>
                                <a href="index.php?router=admin_projects_add" class="btn-action btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Equipment -->
                    <div class="category-card">
                        <div class="category-header cat-equipment">
                            <i class="fas fa-tools"></i>
                            <h3>Gestion des Équipements</h3>
                        </div>
                        <div class="category-body">
                            <p>Gérez les équipements, les réservations et les maintenances. Suivez l'utilisation des ressources.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_equipment" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Dashboard
                                </a>
                                <a href="index.php?router=admin_equipment_add" class="btn-action btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Publications -->
                    <div class="category-card">
                        <div class="category-header cat-publications">
                            <i class="fas fa-file-alt"></i>
                            <h3>Gestion des Publications</h3>
                        </div>
                        <div class="category-body">
                            <p>Validez les publications soumises. Générez les rapports bibliographiques par année ou auteur.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_publications" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Voir tous
                                </a>
                                <a href="index.php?router=actualites_admin" class="btn-action btn-secondary">
                                    <i class="fas fa-newspaper"></i> Actualités
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Events -->
                    <div class="category-card">
                        <div class="category-header cat-events">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>Gestion des Événements</h3>
                        </div>
                        <div class="category-body">
                            <p>Créez et publiez des événements et annonces. Gérez les inscriptions et les participations.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_events" class="btn-action btn-primary-admin">
                                    <i class="fas fa-list"></i> Voir tous
                                </a>
                                <a href="index.php?router=admin_events_add" class="btn-action btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Settings -->
                    <div class="category-card">
                        <div class="category-header cat-settings">
                            <i class="fas fa-cog"></i>
                            <h3>Paramètres Généraux</h3>
                        </div>
                        <div class="category-body">
                            <p>Configurez les paramètres du laboratoire. Logo, thème, couleurs, email, sauvegarde/restauration.</p>
                            <div class="category-actions">
                                <a href="index.php?router=admin_settings" class="btn-action btn-primary-admin">
                                    <i class="fas fa-sliders-h"></i> Paramètres
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }
}
?>
