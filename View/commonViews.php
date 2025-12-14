<?php
class commonViews {
    
    // Navigation bar - adapts based on user session
    public function navBar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['admin'])) {
            $this->navBarA();
        } else if (isset($_SESSION['user'])) {
            $this->navBarC();
        } else {
            $this->navBarD();
        }
    }
    
    // Navbar for disconnected users
    public function navBarD() {
        ?>
        <nav class="navbar">
            <a href="index.php?router=accueil" class="logo">
                <img src="View/assets/logo.png" alt="LRE Logo" width="120px">
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
    
    // Navbar for connected users
    public function navBarC() {
        ?>
        <nav class="navbar">
            <a href="index.php?router=accueil" class="logo">
                <img src="View/assets/logo.png" alt="LRE Logo" width="120px">
            </a>
            <div class="nav-links">
                <a href="index.php?router=accueil">Accueil</a>
                <a href="index.php?router=projets">Projets</a>
                <a href="index.php?router=publications">Publications</a>
                <a href="index.php?router=equipements">Équipements</a>
                <a href="index.php?router=equipes">Membres</a>
                <a href="index.php?router=contact">Contact</a>
            </div>
            <div class="user-section">
                <i class="fa-regular fa-bell"></i>
                <div class="user-dropdown">
                    <img class="user-img" src="<?= !empty($_SESSION['user']['photo']) ? $_SESSION['user']['photo'] : 'View/assets/default_avatar.png' ?>" alt="User">
                    <ul class="dropdown-menu" id="userMenu">
                        <a href="index.php?router=profil">Mon Profil</a>
                        <a href="index.php?router=mes-projets">Mes Projets</a>
                        <a href="index.php?router=mes-reservations">Mes Réservations</a>
                        <a href="index.php?router=logout">Se Déconnecter</a>
                    </ul>
                </div>
            </div>
        </nav>
        <script>
            document.querySelector('.user-img').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('userMenu').classList.toggle('show');
            });
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.user-dropdown')) {
                    document.getElementById('userMenu').classList.remove('show');
                }
            });
        </script>
        <?php
    }
    
    // Navbar for admin users
    public function navBarA() {
        ?>
        <nav class="navbar">
            <a href="index.php?router=accueil" class="logo">
                <img src="View/assets/logo.png" alt="LRE Logo" width="120px">
            </a>
            <div class="nav-links">
                <a href="index.php?router=accueil">Accueil</a>
                <a href="index.php?router=projets">Projets</a>
                <a href="index.php?router=publications">Publications</a>
                <a href="index.php?router=equipements">Équipements</a>
                <a href="index.php?router=equipes">Membres</a>
                <a href="index.php?router=contact">Contact</a>
            </div>
            <div class="user-section">
                <i class="fa-regular fa-bell"></i>
                <div class="user-dropdown">
                    <img class="user-img" src="<?= !empty($_SESSION['admin']['photo']) ? $_SESSION['admin']['photo'] : 'View/assets/default_avatar.png' ?>" alt="Admin">
                    <ul class="dropdown-menu" id="adminMenu">
                        <a href="index.php?router=profil">Mon Profil</a>
                        <a href="index.php?router=admin-dashboard">Espace Admin</a>
                        <a href="index.php?router=logout">Se Déconnecter</a>
                    </ul>
                </div>
            </div>
        </nav>
        <script>
            document.querySelector('.user-img').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('adminMenu').classList.toggle('show');
            });
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.user-dropdown')) {
                    document.getElementById('adminMenu').classList.remove('show');
                }
            });
        </script>
        <?php
    }
    
    // Footer component
    public function footer() {
        ?>
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="View/assets/logo.png" alt="LRE Logo" width="150px">
                    <p>Le Laboratoire de Recherche ESI (LRE) est un pôle d'excellence dédié à la recherche en informatique avancée.</p>
                    <div class="social-media">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Menu</h3>
                    <a href="index.php?router=accueil">Accueil</a>
                    <a href="index.php?router=projets">Projets</a>
                    <a href="index.php?router=publications">Publications</a>
                    <a href="index.php?router=equipements">Équipements</a>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fa-solid fa-location-dot"></i> ESI, Oued Smar, Alger</p>
                    <p><i class="fa-solid fa-phone"></i> +213 (0) 23 54 00 00</p>
                    <p><i class="fa-solid fa-envelope"></i> contact@lre-esi.dz</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Laboratoire de Recherche ESI. Tous droits réservés.</p>
            </div>
        </footer>
        <?php
    }
    
    // Section title component
    public function sectionTitle($title) {
        ?>
        <h2 class="section-title">
            <?= $title ?>
            <i class="fa-solid fa-paperclip"></i>
        </h2>
        <?php
    }
    
    // Blue button component
    public function blueButton($content, $destination) {
        ?>
        <a href="index.php?router=<?= $destination ?>">
            <button type="button" class="btn-primary"><?= $content ?></button>
        </a>
        <?php
    }
    
    // Form input component
    public function famousInput($label, $placeholder, $type, $name) {
        ?>
        <div class="form-group">
            <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
            <input class="form-input" type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>" placeholder="<?= $placeholder ?>">
        </div>
        <?php
    }
    
    // Card component for projects/publications
    public function card($title, $description, $link, $image = null) {
        ?>
        <div class="card">
            <?php if ($image): ?>
                <img src="<?= $image ?>" alt="<?= $title ?>">
            <?php endif; ?>
            <div class="card-content">
                <h4><?= $title ?></h4>
                <p><?= $description ?></p>
                <a href="<?= $link ?>" class="btn-primary">En savoir plus</a>
            </div>
        </div>
        <?php
    }
}
?>
