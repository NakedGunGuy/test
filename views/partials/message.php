<?php
/**
 * Reusable message component
 * @param string $type - success, error, warning
 * @param string $icon - icon to display
 * @param string $title - message title
 * @param string $message - message text
 * @param array $actions - array of action buttons ['text' => 'Click Me', 'url' => '/path', 'class' => 'btn blue']
 */
?>

<div class="message-container">
    <div class="message-icon <?= $type ?>">
        <?= $icon ?>
    </div>
    
    <h1 class="message-title <?= $type ?>"><?= htmlspecialchars($title) ?></h1>
    
    <p class="message-text">
        <?= htmlspecialchars($message) ?>
    </p>
    
    <?php if (!empty($actions)): ?>
        <div class="action-buttons">
            <?php foreach ($actions as $action): ?>
                <a href="<?= htmlspecialchars($action['url']) ?>" class="<?= $action['class'] ?>">
                    <?= htmlspecialchars($action['text']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>