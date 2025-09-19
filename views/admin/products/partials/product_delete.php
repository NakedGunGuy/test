<button
    class="btn black"
    hx-get="<?= url('admin/products/confirm-delete/' . htmlspecialchars($product_id)) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Delete</button>