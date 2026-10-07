<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <h1>Customer Management</h1>

        <p>
            Manage customer profiles and contact information.
        </p>


        <!-- SUCCESS MESSAGE -->

        <?php if (isset($_SESSION['success'])): ?>

            <div
                class="login-error"
                style="
                    background:#E8F5E9;
                    color:#2E7D32;
                    border-left-color:#2E7D32;
                "
            >
                <?= htmlspecialchars($_SESSION['success']); ?>
            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if (isset($_SESSION['error'])): ?>

            <div class="login-error">

                <?= htmlspecialchars($_SESSION['error']); ?>

            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>


        <!-- ADD CUSTOMER -->

        <a
            href="customers.php?action=create"
            class="btn"
        >
            + Add Customer
        </a>


        <br><br>


        <!-- CUSTOMER TABLE -->

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Customer Name</th>

                    <th>Contact Number</th>

                    <th>Email</th>

                    <th>Address</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($customers)): ?>

                    <tr>

                        <td colspan="6">
                            No customers found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($customers as $customer): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $customer['customer_id']
                                ); ?>
                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $customer['customer_name']
                                    ); ?>
                                </strong>

                            </td>


                            <td>

                                <?= !empty($customer['contact_number'])
                                    ? htmlspecialchars(
                                        $customer['contact_number']
                                    )
                                    : '—'; ?>

                            </td>


                            <td>

                                <?= !empty($customer['email'])
                                    ? htmlspecialchars(
                                        $customer['email']
                                    )
                                    : '—'; ?>

                            </td>


                            <td>

                                <?= !empty($customer['address'])
                                    ? htmlspecialchars(
                                        $customer['address']
                                    )
                                    : '—'; ?>

                            </td>


                            <td>

                                <a
                                    href="customers.php?action=edit&id=<?= intval($customer['customer_id']); ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php include 'app/views/layouts/footer.php'; ?>

