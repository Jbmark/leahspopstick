<?php

require_once '../../config/database.php';

// Fetch active products from the database
$stmt = $pdo->query("
    SELECT *
    FROM products
    WHERE status = 'Active'
    ORDER BY product_name ASC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Leah's Popstick - Wood Products Manufacturing
</title>

<link
    rel="stylesheet"
    href="../css/shop.css"
>


</head>

<body>

<!-- NAVIGATION -->

<nav class="shop-navbar">


<div class="nav-brand">
    🌿 Leah's Popstick
</div>

<div class="nav-links">

    <a href="index.php">
        Home
    </a>

    <a href="#catalog">
        Products
    </a>

    <a
        href="order.php"
        class="nav-track-btn"
    >
        Place an Order
    </a>

    <a
        href="track.php"
        class="nav-track-btn"
    >
        Track Order
    </a>

</div>


</nav>

<!-- HERO -->

<header class="shop-hero">


<div class="hero-overlay">

    <span class="badge">
        Wooden Food Products
    </span>

    <h1>
        Quality Wooden Products for Your Business
    </h1>

    <p>
        Leah's Popstick Wood Products Manufacturing
        supplies wooden food products for businesses
        and bulk orders.
    </p>

    <div class="hero-actions">

        <a
            href="order.php"
            class="btn-primary"
        >
            Place an Order →
        </a>

        <a
            href="#catalog"
            class="btn-secondary"
        >
            View Products
        </a>

    </div>

</div>


</header>

<!-- BUSINESS INFORMATION -->

<section class="b2b-notice-bar">


<div class="notice-card">

    <h3>
        Bulk Orders
    </h3>

    <p>
        We accept orders for businesses and customers
        who need wooden food products in larger quantities.
    </p>

</div>


<div class="notice-card">

    <h3>
        Multiple Products
    </h3>

    <p>
        You can request different wooden products
        in the same order.
    </p>

</div>


<div class="notice-card">

    <h3>
        Easy Order Tracking
    </h3>

    <p>
        After submitting an order, use your Order ID
        and contact number to check its status.
    </p>

</div>


</section>

<!-- PRODUCT CATALOG -->

<main
    class="catalog-section"
    id="catalog"
>


<div class="section-title">

    <h2>
        Our Products
    </h2>

    <p>
        Browse our available wooden food products
        and submit an order request.
    </p>

</div>


<div class="catalog-container">


    <!-- PRODUCT INFORMATION SIDEBAR -->

    <aside class="categories-sidebar">

        <h3>
            Product Categories
        </h3>

        <ul>

            <li class="active">
                <a href="#catalog">
                    All Products
                </a>
            </li>

            <li>
                <a href="#catalog">
                    Wooden Utensils
                </a>
            </li>

            <li>
                <a href="#catalog">
                    Wooden Sticks
                </a>
            </li>

            <li>
                <a href="#catalog">
                    Food Accessories
                </a>
            </li>

        </ul>


        <div class="sidebar-help-box">

            <h4>
                Need Help?
            </h4>

            <p>
                For bulk orders or product inquiries,
                submit an order request and provide
                your details.
            </p>

        </div>

    </aside>


    <!-- PRODUCT GRID -->

    <div class="products-matrix">

        <?php if (empty($products)) { ?>

            <div class="product-item-card">

                <div class="card-details">

                    <h3>
                        No Products Available
                    </h3>

                    <p>
                        There are currently no active
                        products available.
                    </p>

                </div>

            </div>

        <?php } else { ?>

            <?php foreach ($products as $p) { ?>

                <div class="product-item-card">

                    <div class="card-image-placeholder">

                        <span>
                            🪵 Wooden Product
                        </span>

                    </div>


                    <div class="card-details">

                        <h3>
                            <?= htmlspecialchars(
                                $p['product_name']
                            ); ?>
                        </h3>


                        <p>
                            <?= htmlspecialchars(
                                $p['description']
                                ?: 'Quality wooden product for food and business use.'
                            ); ?>
                        </p>


                        <div class="card-price-row">

                            <div class="price-box">

                                <span class="label">
                                    Price
                                </span>

                                <span class="price-val">
                                    ₱<?= number_format(
                                        $p['selling_price'],
                                        2
                                    ); ?>
                                </span>

                            </div>


                            <span class="unit-badge">

                                Per
                                <?= htmlspecialchars(
                                    $p['unit']
                                ); ?>

                            </span>

                        </div>


                        <a
                            href="order.php"
                            class="btn-card"
                        >
                            Order This Product
                        </a>

                    </div>

                </div>

            <?php } ?>

        <?php } ?>

    </div>

</div>


</main>

<!-- ABOUT -->

<section class="plant-about">


<h2>
    About Leah's Popstick
</h2>

<p>
    Leah's Popstick Wood Products Manufacturing
    is located in Talacogon, Agusan del Sur.
    The business produces wooden food products
    such as popsicle sticks, spoons, forks,
    sporks, coffee stirrers, and other wooden items.
</p>


</section>

<!-- FOOTER -->

<footer class="shop-footer">

<p>
    © <?= date('Y'); ?>
    Leah's Popstick Wood Products Manufacturing.
</p>


</footer>

</body>

</html>
