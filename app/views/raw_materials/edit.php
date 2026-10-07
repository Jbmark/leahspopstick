<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <h1>Edit Raw Material</h1>

        <p>
            Update raw material information.
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


        <!-- EDIT FORM -->

        <form
            method="POST"
            action="raw_materials.php?action=edit&id=<?= intval($rawMaterial['raw_material_id']); ?>"
        >


            <!-- MATERIAL NAME -->

            <div style="margin-bottom:20px;">

                <label
                    for="material_name"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Material Name
                </label>

                <input
                    type="text"
                    id="material_name"
                    name="material_name"
                    value="<?= htmlspecialchars(
                        $rawMaterial['material_name']
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


            <!-- DESCRIPTION -->

            <div style="margin-bottom:20px;">

                <label
                    for="description"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #D5D9E0;
                        border-radius:6px;
                        font-size:14px;
                        resize:vertical;
                    "
                ><?= htmlspecialchars(
                    $rawMaterial['description'] ?? ''
                ); ?></textarea>

            </div>


            <!-- UNIT -->

            <div style="margin-bottom:20px;">

                <label
                    for="unit"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Unit
                </label>

                <input
                    type="text"
                    id="unit"
                    name="unit"
                    value="<?= htmlspecialchars(
                        $rawMaterial['unit']
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


            <!-- CURRENT QUANTITY -->

            <div style="margin-bottom:20px;">

                <label
                    for="current_quantity"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Current Quantity
                </label>

                <input
                    type="text"
                    id="current_quantity"
                    value="<?= htmlspecialchars(
                        $rawMaterial['quantity']
                    ); ?>
                    <?= htmlspecialchars(
                        $rawMaterial['unit']
                    ); ?>"
                    readonly
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #D5D9E0;
                        border-radius:6px;
                        font-size:14px;
                        background:#F5F5F5;
                    "
                >

                <small
                    style="
                        display:block;
                        margin-top:6px;
                        color:#666;
                    "
                >
                    Use Stock In, Stock Out, or Adjustment to change quantity.
                </small>

            </div>


            <!-- REORDER LEVEL -->

            <div style="margin-bottom:20px;">

                <label
                    for="reorder_level"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Reorder Level
                </label>

                <input
                    type="number"
                    id="reorder_level"
                    name="reorder_level"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $rawMaterial['reorder_level']
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


            <!-- STATUS -->

            <div style="margin-bottom:20px;">

                <label
                    for="status"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #D5D9E0;
                        border-radius:6px;
                        font-size:14px;
                        background:#ffffff;
                    "
                >

                    <option
                        value="Active"
                        <?= $rawMaterial['status'] === 'Active'
                            ? 'selected'
                            : ''; ?>
                    >
                        Active
                    </option>

                    <option
                        value="Inactive"
                        <?= $rawMaterial['status'] === 'Inactive'
                            ? 'selected'
                            : ''; ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <button
                type="submit"
                class="btn"
            >
                Update Raw Material
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

