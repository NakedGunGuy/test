<?php foreach ($products as $product): ?>
<div
    class="px-3 py-2 hover:bg-gray-100 cursor-pointer product-result"
    data-name="<?= htmlspecialchars($product['name']) ?>"
>
    <?= htmlspecialchars($product['name']) ?>
</div>
<?php endforeach; ?>

<script>
document.querySelectorAll('.product-result').forEach(el => {
    el.addEventListener('click', function() {
        const input = document.querySelector('input[name="name"]');
        if (!input) return;

        input.value = this.dataset.name;
        const form = input.closest('form');
        if (form) form.submit();
    });
});
</script>