<?php
require_once("commonViews.php");

class homeView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Laboratoire de Recherche ESI (LRE)</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/homeStyle.css">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="View/scripts/homeScript.js"></script>
        </head>
        <?php
    }
    
    public function slider() {
        ?>
        <div class="hero-slider">
            <div class="slider-header">
                <img src="View/assets/logo-removebg-preview.png" alt="LRE Logo" class="hero-logo">
                <div class="social-media">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
            <div class="slider-content" id="sliderContent">
                <h1 id="slideTitle">Bienvenue au <img src="View/assets/logo-removebg-preview.png" alt="LRE" style="height: 1.2em; vertical-align: bottom; margin-left: 10px;"></h1>
                <p id="slideDescription">Découvrez nos dernières recherches et innovations</p>
            </div>
            <div class="slider-indicators" id="sliderIndicators">
                <span class="indicator active"></span>
                <span class="indicator"></span>
                <span class="indicator"></span>
            </div>
        </div>
        <?php
    }
    
    public function newsSection() {
        ?>
        <section class="news-section">
            <?php
            $common = new commonViews();
            $common->sectionTitle("Actualités Scientifiques");
            ?>
            <div class="news-grid" id="newsGrid">
                <!-- News cards will be loaded dynamically -->
            </div>
        </section>
        <?php
    }
    
    public function aboutSection() {
        ?>
        <section class="about-section">
            <div class="about-content">
                <h2>À Propos du Laboratoire</h2>
                <p>Le Laboratoire de Recherche ESI (LRE) est un pôle d'excellence dédié à la recherche en informatique avancée, couvrant l'IA, la sécurité et les systèmes distribués.</p>
                <p>Nos équipes de recherche travaillent sur des projets innovants en collaboration avec des partenaires industriels et académiques nationaux et internationaux.</p>
            </div>
            <div class="about-stats">
                <div class="stat-card">
                    <i class="fa-solid fa-users"></i>
                    <h3 id="statMembers">50+</h3>
                    <p>Chercheurs</p>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-diagram-project"></i>
                    <h3 id="statProjects">25+</h3>
                    <p>Projets</p>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-book"></i>
                    <h3 id="statPublications">100+</h3>
                    <p>Publications</p>
                </div>
            </div>
        </section>
        <?php
    }
    
    public function eventsSection() {
        ?>
        <section class="events-section">
            <?php
            $common = new commonViews();
            $common->sectionTitle("Événements à Venir");
            ?>
            <div class="events-grid" id="eventsGrid">
                <!-- Events will be loaded dynamically -->
            </div>
        </section>
        <?php
    }
    
    public function partnersSection() {
        ?>
        <section class="partners-section">
            <?php
            $common = new commonViews();
            $common->sectionTitle("Nos Partenaires");
            ?>
            <div class="partners-carousel">
                <div class="partners-track" id="partnersTrack">
                    <!-- Partners logos will be loaded dynamically -->
                </div>
            </div>
        </section>
        <?php
    }
    
    public function afficher_page() {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body class="home-page">
                <?php 
                    $this->slider();
                    $common->navBar();
                    $this->newsSection();
                    $this->aboutSection();
                    $this->eventsSection();
                    $this->partnersSection();
                    $common->footer();
                ?>
            </body>
        </html>
        <?php
    }
    
    public function afficherContact() {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                <section class="contact-section">
                    <h1>Contactez-nous</h1>
                    <div class="contact-container">
                        <div class="contact-info">
                            <h2>Informations</h2>
                            <p><i class="fa-solid fa-location-dot"></i> École Supérieure d'Informatique, Oued Smar, Alger</p>
                            <p><i class="fa-solid fa-phone"></i> +213 (0) 23 54 00 00</p>
                            <p><i class="fa-solid fa-envelope"></i> contact@lre-esi.dz</p>
                        </div>
                        <form class="contact-form" method="POST">
                            <input type="text" name="nom" placeholder="Nom" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="text" name="sujet" placeholder="Sujet" required>
                            <textarea name="message" placeholder="Votre message" rows="6" required></textarea>
                            <button type="submit" class="btn-primary">Envoyer</button>
                        </form>
                    </div>
                </section>
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
