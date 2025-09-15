<div class="dialog-header <?= empty($title) ? 'title-only' : '' ?>">
    <?php if (!empty($title)): ?>
        <h3 class="dialog-title"><?= $title ?></h3>
    <?php endif; ?>
    <?php partial('partials/dialog_close_button') ?>
</div>