<?php include 'app/views/layouts/header.php'; ?>

<div class="container">
    
<?php include 'app/views/layouts/sidebar.php'; ?>

<div class="content">

    <h1>Edit Customer</h1>

    <p>
        Update customer information.
    </p>


    <!-- ERROR MESSAGE -->

    <?php if (isset($_SESSION['error'])): ?>

        <div class="login-error">

            <?= htmlspecialchars($_SESSION['error']); ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <!-- CUSTOMER FORM -->

    <form
        method="POST"
        action="customers.php?action=edit&id=<?= intval($customer['customer_id']); ?>"
    >

        <!-- CUSTOMER NAME -->

        <div class="form-group">

            <label for="customer_name">
                Customer Name
            </label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="<?= htmlspecialchars(
                    $customer['customer_name']
                ); ?>"
                required
            >

        </div>


        <!-- CONTACT NUMBER -->

        <div class="form-group">

            <label for="contact_number">
                Contact Number
            </label>

            <input
                type="text"
                id="contact_number"
                name="contact_number"
                value="<?= htmlspecialchars(
                    $customer['contact_number'] ?? ''
                ); ?>"
            >

        </div>


        <!-- EMAIL -->

        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars(
                    $customer['email'] ?? ''
                ); ?>"
            >

        </div>


        <!-- ADDRESS -->

        <div class="form-group">

            <label for="address">
                Address
            </label>

            <textarea
                id="address"
                name="address"
                rows="4"
                style="
                    width:100%;
                    padding:12px;
                    border:1px solid #E0E0E0;
                    border-radius:8px;
                    box-sizing:border-box;
                "
            ><?= htmlspecialchars(
                $customer['address'] ?? ''
            ); ?></textarea>

        </div>


        <!-- BUTTONS -->

        <button
            type="submit"
            class="btn"
        >
            Update Customer
        </button>


        <a
            href="customers.php"
            class="btn"
        >
            Cancel
        </a>

    </form>

</div>


<?php include 'app/views/layouts/footer.php'; ?>
