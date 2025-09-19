<?php foreach ($editions as $edition): ?>
    <div
        class="search-result-item"
        hx-get="<?= url('admin/products/edition/' . htmlspecialchars($edition['id'])) ?>"
        hx-target="#dialog"
        hx-swap="innerHTML"
        style="padding: 12px; cursor: pointer; border-bottom: 1px solid rgba(0, 174, 239, 0.15); transition: all 0.2s ease;"
        onmouseover="this.style.background='rgba(0, 174, 239, 0.05)'"
        onmouseout="this.style.background='transparent'"
    >
        <div style="font-weight: 600; color: white; margin-bottom: 4px;">
            <?= htmlspecialchars($edition['card_name']) ?>
        </div>
        <div style="font-size: 12px; color: #C0C0D1;">
            <?= htmlspecialchars($edition['set_name']) ?>, #<?= htmlspecialchars($edition['collector_number']) ?> - <?= htmlspecialchars($edition['rarity']) ?>
        </div>
    </div>
<?php endforeach; ?>
