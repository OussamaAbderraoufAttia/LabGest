<?php
/**
 * SectionComponent - Handles section titles and headers
 */
class SectionComponent {
    
    /**
     * Render a section title
     */
    public static function title($title) {
        ?>
        <h2 class="section-title">
            <?= htmlspecialchars($title) ?>
            <i class="fa-solid fa-paperclip"></i>
        </h2>
        <?php
    }
}
?>
