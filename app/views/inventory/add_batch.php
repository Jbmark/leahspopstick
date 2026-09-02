<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <h1>Add New Stock Batch</h1>
        <p>Log incoming production milestones. This populates your chronological tracking layer.</p>
        
        <div class="card" style="max-width: 500px;">
            <form method="POST">
                <div class="form-group">
                    <label>Product</label>
                    <select name="product_id" required style="width:100%; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;">
                        <?php foreach($products as $p){ ?>
                            <option value="<?= $p['product_id']; ?>"><?= htmlspecialchars($p['product_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Batch Number</label>
                    <input type="text" name="batch_number" required placeholder="e.g., B001">
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" min="1" required placeholder="Enter quantity">
                </div>
                <div class="form-group">
                    <label>Date Received</label>
                    <input type="date" name="date_received" required value="<?= date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn-login" style="margin-top:10px;">Add Batch</button>
            </form>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
