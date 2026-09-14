<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
<?php include 'app/views/layouts/sidebar.php'; ?>
<div class="content">

    <div style="margin-bottom: 20px;">
        <a href="expenses.php" style="text-decoration:none; color:#1B5E20; font-weight:600;">&larr; Back to Expense Ledger</a>
        <h1 style="margin-top:15px;">Log Operational Capital Outflow</h1>
        <p style="color:#7A869A; font-size:14px;">Record cash outflows or lumber logistics expenditures straight into the tracking loop.</p>
    </div>

    <div class="card" style="max-width:600px;">
        <form method="POST">
            <div class="form-group">
                <label>Expense Title / Reference</label>
                <input type="text" name="title" required placeholder="e.g., Falcata Lumber Trucking Log">
            </div>

            <div class="form-group">
                <label>Expenditure Category</label>
                <select name="category" required style="width:100%; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;">
                    <option>Raw Materials</option>
                    <option>Logistics / Freight</option>
                    <option>Utilities / Warehouse</option>
                    <option>Equipment Maintenance</option>
                    <option>Administrative Cost</option>
                </select>
            </div>

            <div class="form-group">
                <label>Total Capital Amount (₱)</label>
                <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Description / Audit Remarks</label>
                <textarea name="remarks" style="width:100%; height:80px; padding:12px; border:1px solid #E0E0E0; border-radius:8px; background:#FAFAFA;" placeholder="Provide clarifying transaction context..."></textarea>
            </div>

            <button type="submit" class="btn-login" style="margin-top:10px;">Commit Expense Item</button>
        </form>
    </div>

</div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
