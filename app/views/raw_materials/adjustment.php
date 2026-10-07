<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <h1>Inventory Adjustment</h1>

        <p>
            Correct the recorded quantity of a raw material.
        </p>


        <?php
        $error = $_SESSION['raw_material_error'] ?? '';
        unset($_SESSION['raw_material_error']);
        ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error): ?>

            <div
                class="login-error"
                style="
                    background:#FFEBEE;
                    color:#C62828;
                    border-left-color:#C62828;
                "
            >
                <?= htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <!-- MATERIAL INFORMATION -->

        <div
            style="
                background:#F5F7FA;
                border:1px solid #E5E9F0;
                border-radius:6px;
                padding:16px;
                margin-bottom:25px;
            "
        >

            <p style="margin-bottom:8px;">

                <strong>Material:</strong>

                <?= htmlspecialchars(
                    $rawMaterial['material_name']
                ); ?>

            </p>


            <p style="margin-bottom:0;">

                <strong>Current Quantity:</strong>

                <?= htmlspecialchars(
                    $rawMaterial['quantity']
                ); ?>

                <?= htmlspecialchars(
                    $rawMaterial['unit']
                ); ?>

            </p>

        </div>


        <!-- ADJUSTMENT FORM -->

        <form
            method="POST"
            action="raw_materials.php?action=adjustment&id=<?= intval($rawMaterial['raw_material_id']); ?>"
        >


            <!-- CORRECT QUANTITY -->

            <div style="margin-bottom:20px;">

                <label
                    for="quantity"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Correct Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $rawMaterial['quantity']
                    ); ?>"
                    required
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #D5D9E0;
                        border-radius:6px;
                        font-size:14px;
                    "
                >

            </div>


            <!-- REASON -->

            <div style="margin-bottom:20px;">

                <label
                    for="remarks"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Reason for Adjustment
                </label>

                <textarea
                    id="remarks"
                    name="remarks"
                    rows="4"
                    placeholder="Example: Physical stock count correction"
                    required
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #D5D9E0;
                        border-radius:6px;
                        font-size:14px;
                        resize:vertical;
                    "
                ></textarea>

            </div>


            <!-- BUTTONS -->

            <button
                type="submit"
                class="btn"
                style="background:#757575;"
            >
                Save Adjustment
            </button>

            <a
                href="raw_materials.php"
                class="btn"
                style="
                    background:#757575;
                    margin-left:8px;
                "
            >
                Cancel
            </a>

        </form>

    </div>

</div>


<?php include 'app/views/layouts/footer.php'; ?>

