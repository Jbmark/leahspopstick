<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
<?php include 'app/views/layouts/sidebar.php'; ?>
<div class="content">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <div>
            <h1>Expenditures Audit Ledger</h1>
            <p style="color:#7A869A; font-size:14px; margin:0;">Chronological financial outlays and material acquisition logs.</p>
        </div>
        <a href="expenses.php?action=create" class="btn">+ New Expense Item</a>
    </div>

    <div class="card" style="padding:0; overflow:hidden; border-radius:12px;">
        <table>
            <tr>
                <th>Date Logged</th>
                <th>Expense Reference</th>
                <th>Description</th>
                <th>Outlay Amount</th>
                <th>Account Encoder</th>
            </tr>
            <?php if (empty($expenses)) { ?>
                <tr><td colspan="5" style="text-align:center; color:#7A869A; padding:30px;">No operational outflows logged inside system tracking points.</td></tr>
            <?php } else {
                foreach($expenses as $e){ ?>
                <tr>
                    <td><?= date('M d, Y', strtotime($e['expense_date'])); ?></td>
                    <td><b><?= htmlspecialchars($e['expense_name']); ?></b></td>
                    <td><small style="color:#555;"><?= htmlspecialchars($e['description'] ?: 'None.'); ?></small></td>
                    <td style="color:#C62828; font-weight:700;">₱<?= number_format($e['amount'], 2); ?></td>
                    <td><?= htmlspecialchars($e['full_name']); ?></td>
                </tr>
            <?php } } ?>
        </table>
    </div>

</div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
