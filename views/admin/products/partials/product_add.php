<?php partial('partials/dialog_header', [
    'title' => 'Add Product',
]) ?>

<div class="dialog-content">
    <div class="form-group">
        <label class="form-label">Search Edition</label>
        <input
            name="q"
            hx-get="/admin/editions/search"
            hx-trigger="keyup changed delay:200ms"
            hx-target="#edition-results"
            hx-swap="innerHTML"
            class="form-input"
            placeholder="Type to search for cards..."
        >
        <div class="form-help">Search for a card edition to create a product</div>
    </div>

    <div id="edition-results" class="search-results" style="position: relative; top: 0; max-height: 200px;"></div>
</div>

<div class="form-actions">
    <button type="button" onclick="closeDialog()" class="btn black">
        Close
    </button>
</div>