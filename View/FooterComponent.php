<?php
/**
 * FooterComponent - Handles all footer rendering
 */
class FooterComponent {
    
    /**
     * Render the main footer
     */
    public static function render() {
        ?>
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="View/assets/logo-removebg-preview.png" alt="LRE Logo" class="footer-logo">
                    <p>Le Laboratoire de Recherche ESI (LRE) est un pôle d'excellence dédié à la recherche en informatique avancée.</p>
                    <div class="social-media">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="index.php?router=equipes" title="Nos Équipes"><i class="fa-solid fa-users"></i></a>
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
}
?>
