<?php
/**
 * LEGACY COMPATIBILITY WRAPPER
 * Import new component classes - backward compatible
 * Each old method now delegates to the new components
 */
require_once("NavBarComponent.php");
require_once("FooterComponent.php");
require_once("FormComponent.php");
require_once("CardComponent.php");
require_once("SectionComponent.php");

class commonViews {
    
    // Navigation bar - adapts based on user session
    public function navBar() {
        NavBarComponent::render();
    }
    
    // Navbar for disconnected users
    public function navBarD() {
        NavBarComponent::renderGuestNavBar();
    }
    
    // Navbar for connected users
    public function navBarC() {
        NavBarComponent::renderUserNavBar();
    }
    
    // Navbar for admin users
    public function navBarA() {
        NavBarComponent::renderAdminNavBar();
    }
    
    // Footer component
    public function footer() {
        FooterComponent::render();
    }
    
    // Section title component
    public function sectionTitle($title) {
        SectionComponent::title($title);
    }
    
    // Blue button component
    public function blueButton($content, $destination) {
        FormComponent::button($content, $destination);
    }
    
    // Form input component
    public function famousInput($label, $placeholder, $type, $name) {
        FormComponent::input($label, $placeholder, $type, $name);
    }
    
    // Card component for projects/publications
    public function card($title, $description, $link, $image = null) {
        CardComponent::render($title, $description, $link, $image);
    }
}
?>
