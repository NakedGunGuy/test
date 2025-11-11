<?php partial('partials/dialog_header', [
    'title' => t('common.search_cards'),
]) ?>

<div class="dialog-content">
    <div class="form-group" style="position: relative; margin-bottom: 1rem;">
        <input
            type="text"
            id="nav-search-input"
            class="form-input"
            name="name"
            placeholder="<?= t('products.enter_card_name') ?>"
            hx-get="<?= url('products/search?nav=1') ?>"
            hx-trigger="keyup changed delay:300ms"
            hx-target="#nav-search-results"
            hx-swap="innerHTML"
            hx-include="[name='name']"
            autocomplete="off"
            style="width: 100%; font-size: 1.1rem;"
        >
    </div>
    <div id="nav-search-results" style="min-height: 200px; max-height: 400px; overflow-y: auto;"></div>
</div>

<script>
// Auto-focus search input
setTimeout(() => {
    document.getElementById('nav-search-input')?.focus();
}, 100);
</script>
