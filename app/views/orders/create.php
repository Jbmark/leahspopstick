<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <div style="margin-bottom:20px;">

            <a
                href="orders.php"
                style="text-decoration:none; color:#1B5E20; font-weight:600;"
            >
                &larr; Back to Orders
            </a>

            <h1 style="margin-top:15px;">
                Record New Customer Order
            </h1>

            <p>
                Enter the customer information, order source, delivery details,
                and products included in the order.
            </p>

        </div>


        <?php if (isset($_SESSION['order_error'])) { ?>

            <div
                class="login-error"
                style="max-width:800px; margin-bottom:20px;"
            >
                <?= htmlspecialchars($_SESSION['order_error']); ?>

                <?php unset($_SESSION['order_error']); ?>
            </div>

        <?php } ?>


        <div class="card" style="max-width:800px;">

            <form method="POST">

                <!-- Customer and Order Source -->

                <div
                    style="
                        display:grid;
                        grid-template-columns:1fr 1fr;
                        gap:16px;
                        margin-bottom:20px;
                    "
                >

                    <div class="form-group">

                        <label>Customer</label>

                        <select
                            name="customer_id"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #E0E0E0;
                                border-radius:8px;
                                background:#FAFAFA;
                            "
                        >

                            <option value="">
                                -- Select Customer --
                            </option>

                            <?php foreach ($customers as $customer) { ?>

                                <option
                                    value="<?= $customer['customer_id']; ?>"
                                >
                                    <?= htmlspecialchars($customer['customer_name']); ?>

                                    -
                                    <?= htmlspecialchars(
                                        $customer['contact_number'] ?? 'No contact'
                                    ); ?>
                                </option>

                            <?php } ?>

                        </select>

                        <br>

                        <a
                            href="customers.php?action=create"
                            class="btn"
                            style="display:inline-block;"
                        >
                            + Add Customer
                        </a>

                    </div>


                    <div class="form-group">

                        <label>Order Source</label>

                        <select
                            name="order_source"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #E0E0E0;
                                border-radius:8px;
                                background:#FAFAFA;
                            "
                        >

                            <option value="Website">
                                Website
                            </option>

                            <option value="Messenger">
                                Messenger
                            </option>

                            <option value="Viber">
                                Viber
                            </option>

                            <option value="Phone">
                                Phone
                            </option>

                            <option value="Email">
                                Email
                            </option>

                            <option value="Walk-in">
                                Walk-in
                            </option>

                        </select>

                    </div>

                </div>


                <!-- Delivery Address -->

                <div class="form-group">

                    <label>Delivery Address</label>

                    <textarea
                        name="delivery_address"
                        style="
                            width:100%;
                            height:70px;
                            padding:12px;
                            border:1px solid #E0E0E0;
                            border-radius:8px;
                            background:#FAFAFA;
                        "
                        placeholder="Enter delivery address"
                    ></textarea>

                </div>


                <!-- Remarks -->

                <div class="form-group">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        style="
                            width:100%;
                            height:60px;
                            padding:12px;
                            border:1px solid #E0E0E0;
                            border-radius:8px;
                            background:#FAFAFA;
                        "
                        placeholder="Optional order remarks"
                    ></textarea>

                </div>


                <hr
                    style="
                        border:0;
                        border-top:1px solid #E5E9F0;
                        margin:25px 0;
                    "
                >


                <!-- Product Selection -->

                <h3
                    style="
                        margin-bottom:10px;
                        color:#1B5E20;
                        font-size:18px;
                    "
                >
                    Order Items
                </h3>

                <p
                    style="
                        color:#7A869A;
                        font-size:13px;
                        margin-bottom:15px;
                    "
                >
                    Select the products and quantities included in this order.
                </p>


                <div id="item-matrix-container">

                    <?php for ($loop = 0; $loop < 4; $loop++) { ?>

                        <div
                            style="
                                display:grid;
                                grid-template-columns:3fr 1fr;
                                gap:16px;
                                margin-bottom:12px;
                            "
                        >

                            <select
                                name="product_id[]"
                                style="
                                    padding:12px;
                                    border:1px solid #E0E0E0;
                                    border-radius:8px;
                                    background:#FAFAFA;
                                "
                            >

                                <option value="">
                                    -- Choose Product --
                                </option>

                                <?php foreach ($products as $p) { ?>

                                    <option
                                        value="<?= $p['product_id']; ?>"
                                    >
                                        <?= htmlspecialchars($p['product_name']); ?>

                                        [₱<?= number_format(
                                            $p['selling_price'],
                                            2
                                        ); ?>

                                        per
                                        <?= htmlspecialchars($p['unit']); ?>]
                                    </option>

                                <?php } ?>

                            </select>


                            <input
                                type="number"
                                name="quantity[]"
                                min="1"
                                step="1"
                                placeholder="Quantity"
                                style="
                                    padding:12px;
                                    border:1px solid #E0E0E0;
                                    border-radius:8px;
                                "
                            >

                        </div>

                    <?php } ?>

                </div>


                <button
                    type="submit"
                    class="btn-login"
                    style="
                        margin-top:20px;
                        max-width:240px;
                        display:block;
                    "
                >
                    Commit Order Record
                </button>

            </form>

        </div>

    </div>

</div>


<?php include 'app/views/layouts/footer.php'; ?>