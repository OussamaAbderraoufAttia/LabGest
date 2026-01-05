<?php
require_once("Components/CardView.php");

class adminDashboardView {
    
    public function header() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Administration - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/adminStyle.css">
            <link rel="stylesheet" href="View/css/components.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="View/scripts/commonScript.js"></script>
        </head>
        <?php
    }
    
    public function sidebar($activePage = 'dashboard') {
        ?>
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <img src="View/assets/logo-removebg-preview.png" alt="Logo" class="sidebar-logo">
                <div class="sidebar-brand">Admin Panel</div>
            </div>
            
            <div class="sidebar-nav">
                <a href="index.php?router=admin-dashboard" class="nav-item <?= $activePage == 'dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line"></i> <span>Tableau de Bord</span>
                </a>
                <a href="index.php?router=admin-users" class="nav-item <?= $activePage == 'users' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> <span>Utilisateurs</span>
                </a>
                <a href="index.php?router=admin-teams" class="nav-item <?= $activePage == 'teams' ? 'active' : '' ?>">
                    <i class="fa-solid fa-people-group"></i> <span>Équipes</span>
                </a>
                <a href="index.php?router=admin-projects" class="nav-item <?= $activePage == 'projects' ? 'active' : '' ?>">
                    <i class="fa-solid fa-diagram-project"></i> <span>Projets</span>
                </a>
                <a href="index.php?router=admin-publications" class="nav-item <?= $activePage == 'publications' ? 'active' : '' ?>">
                    <i class="fa-solid fa-book"></i> <span>Publications</span>
                </a>
                <a href="index.php?router=admin-equipments" class="nav-item <?= $activePage == 'equipments' ? 'active' : '' ?>">
                    <i class="fa-solid fa-flask"></i> <span>Équipements</span>
                </a>
                <a href="index.php?router=admin-events" class="nav-item <?= $activePage == 'events' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i> <span>Événements</span>
                </a>
                <a href="index.php?router=admin-settings" class="nav-item <?= $activePage == 'settings' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gear"></i> <span>Paramètres</span>
                </a>
                <a href="index.php?router=accueil" class="nav-item">
                    <i class="fa-solid fa-globe"></i> <span>Voir le Site</span>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <a href="index.php?router=logout" class="btn-logout-sidebar">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Déconnexion</span>
                </a>
            </div>
        </nav>
        <?php
    }
    
    public function topBar($title) {
        $adminName = $_SESSION['user']['nom'] ?? 'Admin';
        $adminPhoto = $_SESSION['user']['photo'] ?? 'View/assets/default_avatar.png';
        
        ?>
        <div class="admin-header">
            <div class="admin-welcome">
                <h2><?= $title ?></h2>
            </div>
            <div class="admin-user-info">
                <span>Bonjour, <strong><?= htmlspecialchars($adminName) ?></strong></span>
                <img src="<?= htmlspecialchars($adminPhoto) ?>" alt="Profil" class="admin-avatar">
            </div>
        </div>
        <?php
    }

    public function afficherDashboard() {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->header(); ?>
            <body>
                <div class="admin-container">
                    <?php $this->sidebar('dashboard'); ?>
                    
                    <main class="admin-content">
                        <?php $this->topBar('Tableau de Bord'); ?>
                        
                        <?php
                        // Dashboard Overview Cards
                        $cardsData = [
                            [
                                'title' => 'Utilisateurs',
                                'value' => '15+', 
                                'icon' => 'fa-users',
                                'color' => '#4fd1c5',
                                'link' => 'index.php?router=admin-users'
                            ],
                            [
                                'title' => 'Équipes',
                                'value' => '4', 
                                'icon' => 'fa-people-group',
                                'color' => '#667eea',
                                'link' => 'index.php?router=admin-teams'
                            ],
                            [
                                'title' => 'Projets',
                                'value' => '12', 
                                'icon' => 'fa-diagram-project',
                                'color' => '#f6ad55',
                                'link' => 'index.php?router=admin-projects'
                            ],
                            [
                                'title' => 'Publications',
                                'value' => '25', 
                                'icon' => 'fa-book',
                                'color' => '#fc8181',
                                'link' => 'index.php?router=admin-publications'
                            ],
                            [
                                'title' => 'Équipements',
                                'value' => '15', 
                                'icon' => 'fa-flask',
                                'color' => '#63b3ed',
                                'link' => 'index.php?router=admin-equipments'
                            ],
                            [
                                'title' => 'Événements',
                                'value' => '15', 
                                'icon' => 'fa-calendar-days',
                                'color' => '#9f7aea',
                                'link' => 'index.php?router=admin-events'
                            ],
                            [
                                'title' => 'Paramètres',
                                'value' => 'Config', 
                                'icon' => 'fa-gear',
                                'color' => '#a0aec0',
                                'link' => 'index.php?router=admin-settings'
                            ]
                        ];
                        
                        // Custom renderer for dashboard cards
                        $statsRenderer = function($item) {
                            return '
                            <a href="'.$item['link'].'" style="text-decoration:none; display:block;">
                                <div class="stat-card-admin">
                                    <div class="stat-icon-wrapper" style="background-color: '.$item['color'].'">
                                        <i class="fa-solid '.$item['icon'].'"></i>
                                    </div>
                                    <div class="stat-details">
                                        <h3>'.$item['value'].'</h3>
                                        <p>'.$item['title'].'</p>
                                    </div>
                                </div>
                            </a>
                            ';
                        };
                        
                        echo '<div class="dashboard-grid">';
                        foreach ($cardsData as $card) {
                            echo $statsRenderer($card);
                        }
                        echo '</div>';
                        ?>
                        
                        <!-- Recent Activity / Quick Actions could go here -->
                        
                    </main>
                </div>
            </body>
        </html>
        <?php
    }
}
?>
