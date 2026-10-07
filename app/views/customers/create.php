<?php include 'app/views/layouts/header.php'; ?>

<div class="container">
    
 <?php include 'app/views/layouts/sidebar.php'; ?>

<div class="content">

    <h1>Add Customer</h1>

    <p>
        Create a new customer profile.
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
        action="customers.php?action=create"
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
                placeholder="Enter customer name"
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
                placeholder="Enter contact number"
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
                placeholder="Enter email address"
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
                placeholder="Enter customer address"
                style="
                    width:100%;
                    padding:12px;
                    border:1px solid #E0E0E0;
                    border-radius:8px;
                    box-sizing:border-box;
                "
            ></textarea>

        </div>


        <!-- BUTTONS -->

        <button
            type="submit"
            class="btn"
        >
            Save Customer
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
