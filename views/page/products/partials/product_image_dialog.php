<?php partial('partials/dialog_header', [
        'title' => 'Card Image',
]) ?>
<div>
    <img style="border-radius:12px; width: 100%;" src="<?= card_image($slug) ?>">
</div>