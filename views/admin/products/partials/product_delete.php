<button
    class="btn-text"
    style="color: #FF6B6B;"
    hx-get="/admin/products/confirm-delete/<?= htmlspecialchars($product_id) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Delete</button>