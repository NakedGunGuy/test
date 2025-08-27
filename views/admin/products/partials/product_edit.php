<button
    class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
    hx-get="/admin/products/update/<?= htmlspecialchars($product_id) ?>"
    hx-target="#dialog"
    hx-swap="innerHTML"
>Edit</button>