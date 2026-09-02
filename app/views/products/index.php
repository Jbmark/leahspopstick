<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Products Inventory</h1>
            <a href="products.php?action=create" class="btn">+ Add Product</a>
        </div>
        
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
                    <td><b><?= $p['product_name']; ?></b><br><small style="color:#777;"><?= $p['description']; ?></small></td>
                    <td>₱<?= number_format($p['selling_price'],2); ?></td>
                    <td><span class="role-tag" style="background:#ECEFF1; color:#455A64;">0 <?= $p['unit']; ?></span></td>
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
