<button
    class="btn-text"
    hx-get="/admin/products/update/<?= htmlspecialchars($product_id) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Edit</button>