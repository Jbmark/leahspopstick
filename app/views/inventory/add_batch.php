<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <h1>Add New Stock Batch</h1>

        <p>
            Record incoming finished-product stock by batch.
            Batches are arranged by date received for FIFO inventory management.
        </p>

        <?php if (!empty($_SESSION['inventory_error'])) { ?>

            <div style="
                background:#FFEBEE;
                color:#C62828;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
            ">
                <?= htmlspecialchars($_SESSION['inventory_error']); ?>
            </div>

            <?php unset($_SESSION['inventory_error']); ?>

        <?php } ?>

        <div class="card" style="max-width:500px;">

            <form method="POST">

                <div class="form-group">

                    <label>Product</label>

                    <select
                        name="product_id"
                        required
                        style="
                            width:100%;
                            padding:12px;
                            border:1px solid #E0E0E0;
                            border-radius:8px;
                            background:#FAFAFA;
                        "
                    >

                        <option value="">Select Product</option>

                        <?php foreach ($products as $p) { ?>

                            <option value="<?= $p['product_id']; ?>">
                                <?= htmlspecialchars($p['product_name']); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                
<div class="form-group">

    <label>Batch Number</label>

    <input
        type="text"
        value="Generated automatically when saved"
        readonly
        style="
            width:100%;
            padding:12px;
            border:1px solid #E0E0E0;
            border-radius:8px;
            background:#F1F5F9;
            color:#64748B;
        "
    >

    <small style="color:#64748B;">
        The system will assign a unique batch number after saving.
    </small>

</div>

                <div class="form-group">

                    <label>Quantity Received</label>

                    <input type="number" name="quantity" min="1" step="1" required placeholder="Enter quantity">

                </div>

                <div class="form-group">

                    <label>Date Received</label>

                    <input
                        type="date"
                        name="date_received"
                        required
                        value="<?= date('Y-m-d'); ?>"
                    >

                </div>

                <button
                    type="submit"
                    class="btn-login"
                    style="margin-top:10px;"
                >
                    Add Stock Batch
                </button>

            </form>

        </div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>