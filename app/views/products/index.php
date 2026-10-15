<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">

        
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1>Products Inventory</h1>
    <a href="products.php?action=create" class="btn">+ Add Product</a>
</div>

<form method="GET" action="products.php"
      style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">

    <input type="hidden" name="action" value="index">

    <label for="category">Filter by Category:</label>

    <select name="category" id="category"
            onchange="this.form.submit()"
            style="padding: 10px; border: 1px solid #ccc; border-radius: 6px;">

        <option value=""
            <?= empty($category) ? 'selected' : ''; ?>>
            All Categories
        </option>

        <option value="Wooden Utensils"
            <?= $category === 'Wooden Utensils' ? 'selected' : ''; ?>>
            Wooden Utensils
        </option>

        <option value="Wooden Sticks"
            <?= $category === 'Wooden Sticks' ? 'selected' : ''; ?>>
            Wooden Sticks
        </option>

        <option value="Food Accessories"
            <?= $category === 'Food Accessories' ? 'selected' : ''; ?>>
            Food Accessories
        </option>

        <option value="Uncategorized"
            <?= $category === 'Uncategorized' ? 'selected' : ''; ?>>
            Uncategorized
        </option>
    </select>

    <noscript>
        <button type="submit" class="btn">Filter</button>
    </noscript>

</form>

        
        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach($products as $p){ ?>
                <tr>
                    <td><?= $p['product_id']; ?></td>
                   <td>
    <b><?= htmlspecialchars($p['product_name']); ?></b>
    <br>
    <small style="color:#777;">
        <?= htmlspecialchars($p['description'] ?? ''); ?>
    </small>
</td>
                    <td>₱<?= number_format($p['selling_price'],2); ?></td>
                    <td>
    <?php
    $stock = (float)$p['current_stock'];
    $reorder = (float)$p['reorder_level'];
    ?>

    <span class="role-tag"
          style="background:<?= $stock <= $reorder ? '#FFEBEE' : '#ECEFF1'; ?>;
                 color:<?= $stock <= $reorder ? '#C62828' : '#455A64'; ?>;">
        <?= number_format($stock, 0); ?> <?= htmlspecialchars($p['unit']); ?>
    </span>

    <?php if ($stock <= $reorder) { ?>
        <br>
        <small style="color:#C62828; font-weight:600;">
            Low Stock
        </small>
    <?php } ?>
</td>
                    <td>
                        <span class="role-tag" style="background: <?= $p['status'] == 'Active' ? '#E8F5E9; color:#2E7D32;' : '#FFEBEE; color:#C62828;' ?>">
                            <?= $p['status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="products.php?action=edit&id=<?= $p['product_id']; ?>" style="color:#1B5E20; font-weight:600; text-decoration:none;">Edit</a>
                        <span style="color:#ddd; margin:0 8px;">|</span>
                        <a href="products.php?action=delete&id=<?= $p['product_id']; ?>" style="color:#D32F2F; font-weight:600; text-decoration:none;" onclick="return confirm('Deactivate this product?')">Deactivate</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
