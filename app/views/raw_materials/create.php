<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <h1>Add Raw Material</h1>

        <p>
            Add a new raw material to the inventory.
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


        <!-- FORM -->

        <form
            method="POST"
            action="raw_materials.php?action=create"
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
                    placeholder="Example: Falcata Wood"
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
                    placeholder="Optional description"
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
                    placeholder="Example: kg"
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


            <!-- INITIAL QUANTITY -->

            <div style="margin-bottom:20px;">

                <label
                    for="quantity"
                    style="
                        display:block;
                        margin-bottom:8px;
                        font-weight:600;
                    "
                >
                    Initial Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="0"
                    step="0.01"
                    value="0"
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
                    value="0"
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


            <!-- BUTTONS -->

            <button
                type="submit"
                class="btn"
            >
                Save Raw Material
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

