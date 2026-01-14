<?php
/**
 * NavBarComponent - Handles all navigation bar rendering
 * Static methods for different user roles (admin, user, guest)
 */
class NavBarComponent {
    
    /**
     * Render appropriate navbar based on session
     */
    public static function render() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['admin'])) {
            self::renderAdminNavBar();
        } else if (isset($_SESSION['user'])) {
            self::renderUserNavBar();
        } else {
            self::renderGuestNavBar();
        }
    }
    
    /**
     * Navbar for guest/disconnected users
     */
    public static function renderGuestNavBar() {
        ?>
        <nav class="navbar">
            <a href="index.php?router=accueil" class="logo">
                <img src="View/assets/logo-removebg-preview.png" alt="LRE Logo">
            </a>
            <div class="nav-links">
                <a href="index.php?router=accueil">Accueil</a>
                <a href="index.php?router=projets">Projets</a>
                <a href="index.php?router=publications">Publications</a>
                <a href="index.php?router=equipements">Équipements</a>
                <a href="index.php?router=equipes">Membres</a>
                <a href="index.php?router=contact">Contact</a>
            </div>
            <button class="btn-login" onclick="window.location.href='index.php?router=login'">
                Se Connecter
            </button>
        </nav>
        <?php
    }
    
    /**
     * Navbar for authenticated users
     */
    public static function renderUserNavBar() {
        self::renderAuthNavBar($_SESSION['user']['photo'] ?? 'View/assets/default_avatar.png', false);
    }
    
    /**
     * Navbar for admin users
     */
    public static function renderAdminNavBar() {
        self::renderAuthNavBar($_SESSION['admin']['photo'] ?? 'View/assets/default_avatar.png', true);
    }
    
    /**
     * Shared authenticated navbar for User/Admin
     */
    private static function renderAuthNavBar($photoUrl, $isAdmin = false) {
        $menuId = $isAdmin ? 'adminMenu' : 'userMenu';
        $dashboardLink = $isAdmin ? '<a href="index.php?router=admin_dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</a>' : '<a href="index.php?router=mes-reservations">Mes Réservations</a>';
        ?>
        <nav class="navbar">
            <a href="index.php?router=accueil" class="logo">
                <img src="View/assets/logo.png" alt="LRE Logo">
            </a>
            <div class="nav-links">
                <a href="index.php?router=accueil">Accueil</a>
                <a href="index.php?router=projets">Projets</a>
                <a href="index.php?router=publications">Publications</a>
                <a href="index.php?router=equipements">Équipements</a>
                <a href="index.php?router=equipes">Membres</a>
                <a href="index.php?router=events">Événements</a>
                <a href="index.php?router=contact">Contact</a>
            </div>
            <div class="user-section">
                <!-- Notification Bell -->
                <div class="notification-wrapper" style="position: relative; margin-right: 15px; cursor: pointer;">
                    <i class="fa-regular fa-bell" id="notifBell" style="font-size: 1.2rem; color: #333;"></i>
                    <span id="notifBadge" style="display: none; position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem;">0</span>
                    
                    <div class="notif-dropdown" id="notifDropdown" style="display: none; position: absolute; top: 30px; right: 0; width: 300px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 1000; overflow: hidden;">
                        <div style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Notifications</div>
                        <ul id="notifList" style="list-style: none; padding: 0; margin: 0; max-height: 300px; overflow-y: auto;">
                            <!-- Notifications loaded here -->
                        </ul>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="user-dropdown">
                    <img class="user-img" src="<?= !empty($photoUrl) ? $photoUrl : 'View/assets/default_avatar.png' ?>" alt="User" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; cursor: pointer;">
                    <ul class="dropdown-menu" id="<?= $menuId ?>">
                        <a href="index.php?router=profil">Mon Profil</a>
                        <?php if (!$isAdmin): ?>
                            <a href="index.php?router=mes-projets">Mes Projets</a>
                        <?php endif; ?>
                        <?= $dashboardLink ?>
                        <a href="index.php?router=logout">Se Déconnecter</a>
                    </ul>
                </div>
            </div>
        </nav>
        <script>
            // Profile Dropdown
            document.querySelector('.user-img').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('<?= $menuId ?>').classList.toggle('show');
            });

            // Notification Dropdown
            const bell = document.getElementById('notifBell');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifList = document.getElementById('notifList');
            const notifBadge = document.getElementById('notifBadge');

            bell.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.style.display = notifDropdown.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.user-dropdown')) {
                    document.getElementById('<?= $menuId ?>').classList.remove('show');
                }
                if (!e.target.closest('.notification-wrapper')) {
                    notifDropdown.style.display = 'none';
                }
            });
            
            // Load Notifications logic
            function loadNotifications() {
                fetch('index.php?router=get-notifications')
                    .then(response => response.json())
                    .then(data => {
                        if(data.error) return; // Not logged in
                        
                        // Update Badge
                        if(data.unread_count > 0) {
                            notifBadge.textContent = data.unread_count;
                            notifBadge.style.display = 'block';
                        } else {
                            notifBadge.style.display = 'none';
                        }
                        
                        // Update List
                        notifList.innerHTML = '';
                        if(data.notifications.length === 0) {
                            notifList.innerHTML = '<li style="padding: 15px; color: #777; text-align: center;">Aucune notification</li>';
                        } else {
                            data.notifications.forEach(notif => {
                                let item = document.createElement('li');
                                item.style.padding = '10px';
                                item.style.borderBottom = '1px solid #f0f0f0';
                                item.style.backgroundColor = notif.is_read == 1 ? 'white' : '#f9f9f9';
                                item.style.cursor = 'pointer';
                                item.style.fontSize = '0.9rem';
                                
                                item.innerHTML = `
                                    <div style="font-weight: ${notif.is_read == 1 ? 'normal' : 'bold'}">${notif.message}</div>
                                    <div style="font-size: 0.75rem; color: #888; margin-top: 5px;">${new Date(notif.created_at).toLocaleDateString()}</div>
                                `;
                                
                                item.onclick = () => {
                                    // Mark as read
                                    let formData = new FormData();
                                    formData.append('id', notif.id);
                                    fetch('index.php?router=mark-notification-read', { method: 'POST', body: formData });
                                    
                                    // Redirect if link exists
                                    if(notif.link && notif.link !== '#') window.location.href = notif.link;
                                    else {
                                        // Just refresh list visually
                                        item.style.backgroundColor = 'white';
                                        item.querySelector('div').style.fontWeight = 'normal';
                                        // Update count locally
                                        let count = parseInt(notifBadge.textContent) || 0;
                                        if(count > 0) notifBadge.textContent = count - 1;
                                        if(count - 1 <= 0) notifBadge.style.display = 'none';
                                    }
                                };
                                notifList.appendChild(item);
                            });
                        }
                    });
            }
            
            // Initial Load
            loadNotifications();
        </script>
        <?php
    }
}
?>
