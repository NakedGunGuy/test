<?php foreach ($products as $product): ?>
<div
    class="search-result-item product-result"
    data-name="<?= htmlspecialchars($product['name']) ?>"
    style="padding: 12px; cursor: pointer; border-bottom: 1px solid rgba(0, 174, 239, 0.15); transition: all 0.2s ease;"
    onmouseover="this.style.background='rgba(0, 174, 239, 0.05)'"
    onmouseout="this.style.background='transparent'"
>
    <div style="font-weight: 600; color: white;">
        <?= htmlspecialchars($product['name']) ?>
    </div>
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