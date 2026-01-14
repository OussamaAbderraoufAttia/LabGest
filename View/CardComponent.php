<?php
/**
 * CardComponent - Handles card rendering for projects/publications
 */
class CardComponent {
    
    /**
     * Render a card with title, description, and link
     */
    public static function render($title, $description, $link, $image = null) {
        ?>
        <div class="card">
            <?php if ($image): ?>
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($title) ?>">
            <?php endif; ?>
            <div class="card-content">
                <h4><?= htmlspecialchars($title) ?></h4>
                <p><?= htmlspecialchars($description) ?></p>
                <a href="<?= htmlspecialchars($link) ?>" class="btn-primary">En savoir plus</a>
            </div>
        </div>
        <?php
    }
}
?>
