<?php if (empty($editions)): ?>
    <p class="muted">No cards found for the selected filters.</p>
<?php else: ?>
    <form id="bulk-products-form"
          hx-post="/en/admin/products/bulk/create"
          hx-target="#editions-container"
          hx-on::before-request="optimizeFormData(event)">

        <div class="bulk-header">
            <h3>Add Products for <?= htmlspecialchars($editions[0]['set_name']) ?></h3>
            <p class="muted"><?= count($editions) ?> cards found</p>
        </div>

        <div class="bulk-controls">
            <button type="submit" class="btn blue">Create All Products</button>
        </div>

        <div id="bulk-result" class="bulk-result"></div>

        <div class="bulk-grid">
            <?php foreach ($editions as $index => $edition): ?>
                <div class="bulk-card" data-edition-id="<?= $edition['id'] ?>">
                    <div class="card-info">
                        <div class="card-image">
                            <?php if ($edition['slug']): ?>
                                <img src="<?= card_image($edition['slug']) ?>"
                                     alt="<?= htmlspecialchars($edition['card_name']) ?>"
                                     loading="lazy" height="100" />
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="card-details">
                            <h4><?= htmlspecialchars($edition['card_name']) ?></h4>
                            <p class="collector-number">#<?= htmlspecialchars($edition['collector_number']) ?></p>
                            <p class="rarity"><?= htmlspecialchars($edition['rarity']) ?></p>
                            <?php if ($edition['existing_products'] > 0): ?>
                                <p class="existing-products">
                                    <?= $edition['existing_products'] ?> existing product(s)
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-rows" id="product-rows-<?= $index ?>">
                        <div class="product-row" data-row="0">
                            <input type="hidden" name="products[<?= $index ?>][0][edition_id]" value="<?= $edition['id'] ?>">

                            <div class="field-group">
                                <label>Product Name</label>
                                <input type="text"
                                       name="products[<?= $index ?>][0][name]"
                                       value="<?= htmlspecialchars($edition['card_name']) ?>"
                                       placeholder="Product name">
                            </div>

                            <div class="field-group">
                                <label>Price (€)</label>
                                <input type="number"
                                       name="products[<?= $index ?>][0][price]"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>

                            <div class="field-group">
                                <label>Quantity</label>
                                <input type="number"
                                       name="products[<?= $index ?>][0][quantity]"
                                       min="0"
                                       placeholder="0">
                            </div>

                            <div class="field-group checkbox-group">
                                <label>
                                    <input type="checkbox"
                                           name="products[<?= $index ?>][0][is_foil]"
                                           value="1">
                                    Foil
                                </label>
                            </div>

                            <div class="field-group checkbox-group">
                                <label>
                                    <input type="checkbox"
                                           name="products[<?= $index ?>][0][is_used]"
                                           value="1">
                                    Used
                                </label>
                            </div>

                            <div class="field-group">
                                <label>Description</label>
                                <input type="text"
                                       name="products[<?= $index ?>][0][description]"
                                       placeholder="Optional description">
                            </div>

                            <div class="row-actions">
                                <button type="button" onclick="duplicateRow(<?= $index ?>)" class="btn small blue">
                                    Duplicate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bulk-submit">
            <button type="submit" class="btn blue large">Create All Products</button>
        </div>
    </form>
<?php endif; ?>

<script>
let rowCounters = {};

function duplicateRow(editionIndex) {
    if (!rowCounters[editionIndex]) {
        rowCounters[editionIndex] = 1;
    } else {
        rowCounters[editionIndex]++;
    }

    const container = document.getElementById(`product-rows-${editionIndex}`);
    const firstRow = container.querySelector('.product-row[data-row="0"]');
    const newRow = firstRow.cloneNode(true);
    const newRowIndex = rowCounters[editionIndex];

    // Update row data attribute
    newRow.setAttribute('data-row', newRowIndex);

    // Update all input names to use new row index
    const inputs = newRow.querySelectorAll('input');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            const newName = name.replace(`[${editionIndex}][0]`, `[${editionIndex}][${newRowIndex}]`);
            input.setAttribute('name', newName);
        }

        // Clear values except for edition_id and name
        if (!name.includes('edition_id') && !name.includes('[name]')) {
            input.value = '';
            input.checked = false;
        }
    });

    // Add remove button
    const actions = newRow.querySelector('.row-actions');
    actions.innerHTML = `
        <button type="button" onclick="duplicateRow(${editionIndex})" class="btn small blue">
            Duplicate
        </button>
        <button type="button" onclick="removeRow(this)" class="btn small red">
            Remove
        </button>
    `;

    container.appendChild(newRow);
}

function removeRow(button) {
    button.closest('.product-row').remove();
}

function optimizeFormData(event) {
    // Prevent the default form submission
    event.preventDefault();

    const form = document.getElementById('bulk-products-form');
    const formData = new FormData();

    // Only collect data from rows that have values
    const productRows = form.querySelectorAll('.product-row');
    let productIndex = 0;

    productRows.forEach(row => {
        const editionId = row.querySelector('input[name*="[edition_id]"]')?.value;
        const name = row.querySelector('input[name*="[name]"]')?.value?.trim();
        const price = row.querySelector('input[name*="[price]"]')?.value?.trim();
        const quantity = row.querySelector('input[name*="[quantity]"]')?.value?.trim();

        // Only include rows that have the essential fields filled
        if (editionId && name && price && quantity) {
            const description = row.querySelector('input[name*="[description]"]')?.value?.trim() || '';
            const isfoil = row.querySelector('input[name*="[is_foil]"]')?.checked ? '1' : '0';
            const isUsed = row.querySelector('input[name*="[is_used]"]')?.checked ? '1' : '0';

            // Add to FormData with a clean index
            formData.append(`products[${productIndex}][0][edition_id]`, editionId);
            formData.append(`products[${productIndex}][0][name]`, name);
            formData.append(`products[${productIndex}][0][price]`, price);
            formData.append(`products[${productIndex}][0][quantity]`, quantity);
            formData.append(`products[${productIndex}][0][description]`, description);
            formData.append(`products[${productIndex}][0][is_foil]`, isfoil);
            formData.append(`products[${productIndex}][0][is_used]`, isUsed);

            productIndex++;
        }
    });

    // Continue with the HTMX request using the optimized data
    htmx.ajax('POST', '/en/admin/products/bulk/create', {
        values: Object.fromEntries(formData),
        target: '#editions-container',
        swap: 'innerHTML'
    });
}


</script>

<style>
.bulk-header {
    margin-bottom: 24px;
}

.bulk-header h3 {
    margin: 0 0 8px 0;
    font-size: 1.5rem;
}

.bulk-controls {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.bulk-result {
    margin-bottom: 24px;
    min-height: 20px;
}

.bulk-grid {
    display: grid;
    gap: 24px;
    margin-bottom: 32px;
}

.bulk-card {
    background: rgba(0, 174, 239, 0.05);
    border: 1px solid rgba(0, 174, 239, 0.2);
    border-radius: 16px;
    padding: 20px;
}

.card-info {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 16px;
    margin-bottom: 20px;
}

.card-image {
    width: 80px;
    height: 112px;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
    text-align: center;
}

.card-details h4 {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
}

.card-details p {
    margin: 4px 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

.existing-products {
    color: #00AEEF !important;
    font-weight: 600;
}

.product-rows {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.product-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto auto 2fr auto;
    gap: 12px;
    align-items: end;
    padding: 16px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.field-group label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

.field-group input[type="text"],
.field-group input[type="number"] {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 8px 12px;
    color: white;
    font-size: 0.9rem;
}

.field-group input:focus {
    outline: none;
    border-color: #00AEEF;
    box-shadow: 0 0 0 2px rgba(0, 174, 239, 0.1);
}

.checkbox-group {
    align-items: center;
    justify-content: center;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    cursor: pointer;
}

.checkbox-group input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

.row-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.btn.small {
    padding: 6px 12px;
    font-size: 0.8rem;
}

.btn.red {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.btn.red:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
}

.bulk-submit {
    text-align: center;
    padding: 24px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn.large {
    padding: 16px 32px;
    font-size: 1.1rem;
}

@media (max-width: 1200px) {
    .product-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .card-info {
        grid-template-columns: 60px 1fr;
    }

    .card-image {
        width: 60px;
        height: 84px;
    }
}

@media (max-width: 768px) {
    .bulk-controls {
        flex-direction: column;
    }

    .card-info {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .card-image {
        justify-self: center;
    }
}
</style>