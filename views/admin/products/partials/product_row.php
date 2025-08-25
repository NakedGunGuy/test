<tr>
    <td><?= htmlspecialchars($product['name']); ?></td>
    <td><?= htmlspecialchars($product['set_name']); ?></td>
    <td><?= htmlspecialchars($product['price']); ?></td>
    <td><?= htmlspecialchars($product['quantity']); ?></td>
    <td><?= htmlspecialchars($product['is_foil'] ? 'Yes' : 'No'); ?></td>
    <td>
        <?php
            if (!empty($actions)) {
                partial('admin/products/partials/product_actions', ['product_id' => $product['id'], 'actions' => $actions]); 
            }
        ?>
    </td>
</tr>