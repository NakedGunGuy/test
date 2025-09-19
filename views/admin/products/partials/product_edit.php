<button
    class="btn blue"
    hx-get="<?= url('admin/products/update/' . htmlspecialchars($product_id)) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Edit</button>