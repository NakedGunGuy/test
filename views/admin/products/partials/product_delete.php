<button
    class="btn black"
    hx-get="/admin/products/confirm-delete/<?= htmlspecialchars($product_id) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Delete</button>