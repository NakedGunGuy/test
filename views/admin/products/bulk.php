<div class="filters">
    <div class="filter-form">
        <div class="filters-row">
            <div class="filter-group">
                <label for="set_id">Set *</label>
                <select name="set_id" id="set_id" required
                        hx-get="/en/admin/products/bulk/editions"
                        hx-target="#editions-container"
                        hx-include="[name='rarity'], [name='sort']">
                    <option value="">Select a set...</option>
                    <?php foreach ($sets as $set): ?>
                        <option value="<?= $set['id'] ?>"><?= htmlspecialchars($set['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="rarity">Rarity (Optional)</label>
                <select name="rarity" id="rarity"
                        hx-get="/en/admin/products/bulk/editions"
                        hx-target="#editions-container"
                        hx-include="[name='set_id'], [name='sort']">
                    <option value="">All rarities</option>
                    <?php foreach ($rarities as $rarity): ?>
                        <option value="<?= htmlspecialchars($rarity) ?>"><?= htmlspecialchars($rarity) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select name="sort" id="sort"
                        hx-get="/en/admin/products/bulk/editions"
                        hx-target="#editions-container"
                        hx-include="[name='set_id'], [name='rarity']">
                    <option value="collector_number">Collector Number</option>
                    <option value="name">Card Name</option>
                    <option value="rarity">Rarity</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="editions-container">
    <p class="muted">Select a set to load cards for bulk product creation.</p>
</div>

<style>
.filters {
    background: rgba(0, 174, 239, 0.05);
    border: 1px solid rgba(0, 174, 239, 0.2);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
}

.filters-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

.filter-group select {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(0, 174, 239, 0.3);
    border-radius: 8px;
    padding: 12px 16px;
    color: white;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.filter-group select:focus {
    outline: none;
    border-color: #00AEEF;
    box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.1);
}

.filter-group select option {
    background: #1a1a1a;
    color: white;
}

@media (max-width: 768px) {
    .filters-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}
</style>