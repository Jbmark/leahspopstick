<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <div style="margin-bottom: 20px;">
            <a href="orders.php" style="text-decoration:none; color:#1B5E20; font-weight:600;">&larr; Back to Order Ledger Panel</a>
            <h1 style="margin-top:15px;">Record New Customer Order</h1>
            <p>Establish incoming transaction vectors. Multi-line rows compile directly via structural post array elements.</p>
        </div>

        <?php if (isset($_SESSION['order_error'])) { ?>
            <div class="login-error" style="max-width:800px; margin-bottom:20px;">
                <?= htmlspecialchars($_SESSION['order_error']); unset($_SESSION['order_error']); ?>
            </div>
        <?php } ?>

        <div class="card" style="max-width: 800px;">
            <form method="POST">
                <!-- Customer and Source Information Block Row -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:20px;">
                    <div class="form-group">
                        <label>Target Customer Client</label>
                        <select name="customer_id" required style="width:100%; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;">
                            <option value="">-- Choose Seeded Customer Profile --</option>
                            <?php foreach($customers as $c) { ?>
                                <option value="<?= $c['customer_id']; ?>"><?= htmlspecialchars($c['customer_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Order Source Intake Channel</label>
                        <select name="order_source" required style="width:100%; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;">
                            <option value="Website">Website (Automated Source)</option>
                            <option value="Messenger">Messenger</option>
                            <option value="Viber">Viber</option>
                            <option value="Phone">Phone</option>
                            <option value="Email">Email</option>
                            <option value="Walk-in">Walk-in</option>
                        </select>
                    </div>
                </div>

                <!-- Logistics Parameters Fields Blocks -->
                <div class="form-group">
                    <label>Logistics Delivery Address</label>
                    <textarea name="delivery_address" style="width:100%; height:70px; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;" placeholder="Enter complete shipping destination lines..."></textarea>
                </div>
                <div class="form-group">
                    <label>Internal Transaction Remarks Context</label>
                    <textarea name="remarks" style="width:100%; height:60px; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;" placeholder="Optional workflow specifications details..."></textarea>
                </div>

                <hr style="border:0; border-top:1px solid #E5E9F0; margin:25px 0;">
                <h3 style="margin-bottom:15px; color:#1B5E20; font-size:18px;">Itemized Product Selection Matrix</h3>
                <p style="color:#7A869A; font-size:13px; margin-bottom:15px;">Specify item variants and quantities. Unselected matrix lines are filtered out automatically during validation parsing workflows.</p>

                <!-- Multi-Item Line Input Grid Block Array Forms Elements -->
                <div id="item-matrix-container">
                    <?php for($loop = 0; $loop < 4; $loop++) { ?>
                    <div style="display:grid; grid-template-columns: 3fr 1fr; gap:16px; margin-bottom:12px;">
                        <select name="product_id[]" style="padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;">
                            <option value="">-- Choose Active Finished Product --</option>
                            <?php foreach($products as $p) { ?>
                                <option value="<?= $p['product_id']; ?>">
                                    <?= htmlspecialchars($p['product_name']); ?> [₱<?= number_format($p['selling_price'],2); ?> per <?= htmlspecialchars($p['unit']); ?>]
                                </option>
                            <?php } ?>
                        </select>
                        <input type="number" name="quantity[]" min="1" placeholder="Quantity" style="padding:12px; border:1px solid #E0E0E0; border-radius:8px;">
                    </div>
                    <?php } ?>
                </div>

                <button type="submit" class="btn-login" style="margin-top:20px; max-width:240px; display:block;">Commit Order Record</button>
            </form>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/header.php'; ?>
