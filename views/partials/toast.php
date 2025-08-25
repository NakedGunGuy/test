<div id="toast-<?= uniqid() ?>" 
     class="toast <?= $type ?>" 
     hx-swap-oob="true">
    <?= htmlspecialchars($message) ?>
</div>
