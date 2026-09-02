<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <h1>Add New Product</h1>
        <p>Register a standard structural item variant for inventory tracking.</p>
        
        <div class="card" style="max-width: 600px;">
            <form method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input name="product_name" required placeholder="e.g., Popsicle Stick">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input name="description" placeholder="e.g., Finished Product / Raw Component">
                </div>
                <div class="form-group">
                    <label>Unit Measurement</label>
                    <input name="unit" required placeholder="e.g., box, pack, piece">
                </div>
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Selling Price (₱)</label>
                        <input name="selling_price" type="number" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Reorder Level Alert Threshold</label>
                        <input name="reorder_level" type="number" required placeholder="10">
                    </div>
                </div>
                <button type="submit" class="btn-login" style="margin-top:10px;">Save Product</button>
            </form>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
