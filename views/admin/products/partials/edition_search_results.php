<?php foreach ($editions as $edition): ?>
    <div
            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
            hx-get="/admin/products/edition/<?= htmlspecialchars($edition['id']) ?>"
            hx-target="#dialog"
            hx-swap="innerHTML"
    >
        <?= htmlspecialchars($edition['card_name']) ?>
        (<?= htmlspecialchars($edition['set_name']) ?>, #<?= htmlspecialchars($edition['collector_number']) ?> - <?= htmlspecialchars($edition['rarity']) ?>)
    </div>
<?php endforeach; ?>
