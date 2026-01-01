<?php
class CardView {
    /**
     * Render a grid of cards
     * @param array $data Array of data items
     * @param callable $cardRenderer Function($item) that returns HTML for a single card
     * @param string $id Unique ID for the grid container
     * @param string $class CSS class for grid container
     */
    public static function render($data, $cardRenderer, $id = 'cardGrid', $class = 'card-grid') {
        ?>
        <div id="<?= $id ?>" class="<?= $class ?>">
            <?php if (empty($data)): ?>
                <div class="no-data">Aucun élément à afficher</div>
            <?php else: ?>
                <?php foreach ($data as $item): ?>
                    <?= $cardRenderer($item) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }
}
?>
