<?php
/**
 * FormComponent - Handles all form-related components
 */
class FormComponent {
    
    /**
     * Render a form input field
     */
    public static function input($label, $placeholder, $type, $name, $value = '') {
        ?>
        <div class="form-group">
            <label class="form-label" for="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($label) ?></label>
            <input class="form-input" type="<?= htmlspecialchars($type) ?>" name="<?= htmlspecialchars($name) ?>" id="<?= htmlspecialchars($name) ?>" placeholder="<?= htmlspecialchars($placeholder) ?>" value="<?= htmlspecialchars($value) ?>">
        </div>
        <?php
    }
    
    /**
     * Render a blue primary button
     */
    public static function button($content, $destination) {
        ?>
        <a href="index.php?router=<?= htmlspecialchars($destination) ?>">
            <button type="button" class="btn-primary"><?= htmlspecialchars($content) ?></button>
        </a>
        <?php
    }
}
?>
