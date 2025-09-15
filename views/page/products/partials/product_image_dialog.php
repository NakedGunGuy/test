<?php partial('partials/dialog_header', [
        'title' => 'Card Image',
]) ?>
<div>
    <img style="border-radius:12px; width: 100%;" src="https://api.gatcg.com/cards/images/<?= $slug ?>.jpg">
</div>