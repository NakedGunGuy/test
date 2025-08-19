<?php foreach ($editions as $edition): ?>
    <div
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
        hx-on:click="selectEdition('<?= htmlspecialchars($edition['uuid']) ?>', '<?= htmlspecialchars($edition['card_name']) ?>', '<?= htmlspecialchars($edition['set_name']) ?>')"
    >
        <?= htmlspecialchars($edition['card_name']) ?>
        (<?= htmlspecialchars($edition['set_name']) ?>, #<?= htmlspecialchars($edition['collector_number']) ?> - <?= htmlspecialchars($edition['rarity']) ?>)
    </div>
<?php endforeach; ?>

<?php if (empty($editions)): ?>
    <div class="px-3 py-2 text-gray-500">No results</div>
<?php endif; ?>
