<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <h1>Edit Product Dimensions</h1>
        <p>Modify fields for tracking configuration layout variations.</p>
        
        <div class="card" style="max-width: 600px;">
            <form method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input name="product_name" value="<?= htmlspecialchars($item['product_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input name="description" value="<?= htmlspecialchars($item['description']); ?>">
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <input name="unit" value="<?= htmlspecialchars($item['unit']); ?>" required>
                </div>
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Selling Price (₱)</label>
                        <input name="selling_price" type="number" step="0.01" value="<?= $item['selling_price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input name="reorder_level" type="number" value="<?= $item['reorder_level']; ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-login" style="margin-top:10px;">Update Changes</button>
            </form>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
