<?php
require_once '../../config/database.php';

// Fetch active products dynamically from your normalized database catalog
$stmt = $pdo->query("SELECT * FROM products WHERE status='Active' ORDER BY product_name ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leah's Popstick - Eco-Friendly Wooden Utensils Manufacturing</title>
    <link rel="stylesheet" href="../css/shop.css">
</head>
<body>

    <!-- 🌐 B2B DECOUPLED NAVBAR (No Client Login Account Elements) -->
    <nav class="shop-navbar">
        <div class="nav-brand">🌿 Leah's Popstick</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="#catalog">Products</a>
            <a href="track.php" class="nav-track-btn">📍 Track Wholesale Request</a>
        </div>
    </nav>

    <!-- 🌳 B2B ECO-THEMED HERO BANNER -->
    <header class="shop-hero">
        <div class="hero-overlay">
            <span class="badge">B2B Manufacturing Supply Chain</span>
            <h1>Sustainable Wooden Utensils for Industrial Food Services</h1>
            <p>Export-quality eco-utensils manufactured natively from renewable local Falcata lumber. We supply bulk box lots directly to distributors, commercial packing entities, and food operations globally.</p>
            <div class="hero-actions">
                <a href="order.php" class="btn-primary">Initiate Bulk Supply Request &rarr;</a>
                <a href="#catalog" class="btn-secondary">View Product Specifications</a>
            </div>
        </div>
    </header>

    <!-- 📦 COMMERCIAL VALUE EMPHASIS CARDS (Panel-Facing Structural Verification) -->
    <section class="b2b-notice-bar">
        <div class="notice-card">
            <h3>Wholesale Bulk Allocation</h3>
            <p>All supply lines are distributed strictly in standardized bulk boxes. Minimum processing orders apply based on operational production milestone configurations.</p>
        </div>
        <div class="notice-card">
            <h3>FIFO-Backed Operations</h3>
            <p>Our centralized management panel locks in structural chronological batches. Stock inventory allocations decrement using the First-In, First-Out rule during delivery release schedules.</p>
        </div>
        <div class="notice-card">
            <h3>Multi-Channel Submission</h3>
            <p>Web submissions integrate into our factory dashboard as a 'Website' source while bypassing internal role encoder identifiers automatically.</p>
        </div>
    </section>

    <!-- 🍨 DYNAMIC CATALOG GRID SECTION -->
    <main class="catalog-section" id="catalog">
        <div class="section-title">
            <h2>Manufactured Items Catalog</h2>
            <p>Review standard dimensions, packaging units, and wholesale rates.</p>
        </div>

        <div class="catalog-container">
            <!-- LEFT COLUMN: Categories Filtering Panel Layout -->
            <aside class="categories-sidebar">
                <h3>Product Segments</h3>
                <ul>
                    <li class="active"><a href="#catalog">All Eco-Utensils</a></li>
                    <li><a href="#catalog">Popsicle Sticks</a></li>
                    <li><a href="#catalog">Wooden Spoons</a></li>
                    <li><a href="#catalog">Wooden Forks</a></li>
                    <li><a href="#catalog">Coffee Stirrers</a></li>
                </ul>
                <div class="sidebar-help-box">
                    <h4>Need Custom Variations?</h4>
                    <p>Contact our plant management team directly for custom volume sizing calibrations.</p>
                </div>
            </aside>

            <!-- RIGHT COLUMN: Responsive Item Grid Matrix -->
            <div class="products-matrix">
                <?php foreach($products as $p) { ?>
                    <div class="product-item-card">
                        <div class="card-image-placeholder">
                            <span>🪵 Eco Lot</span>
                        </div>
                        <div class="card-details">
                            <h3><?= htmlspecialchars($p['product_name']); ?></h3>
                            <p><?= htmlspecialchars($p['description'] ?: 'Premium bio-degradable wholesale structural variants lot.'); ?></p>
                            <div class="card-price-row">
                                <div class="price-box">
                                    <span class="label">Wholesale Rate</span>
                                    <span class="price-val">₱<?= number_format($p['selling_price'], 2); ?></span>
                                </div>
                                <span class="unit-badge">Per <?= htmlspecialchars($p['unit']); ?></span>
                            </div>
                            <a href="order.php" class="btn-card">Add to Supply Request</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <!-- 🌲 ABOUT PRODUCTION FACILITY FOOTNOTE -->
    <section class="plant-about">
        <h2>Leah's Popstick Manufacturing Plant</h2>
        <p>Operating out of Talacogon, Agusan del Sur, our wood production plant specializes in converting sustainable timber resources into biodegradable alternatives to single-use plastics, driving objective environmental compatibility parameters natively.</p>
    </section>

    <!-- 🌐 FOOTER FOOTNOTE -->
    <footer class="shop-footer">
        <p>© <?= date('Y'); ?> Leah's Popstick Wood Products Manufacturing. Centralized MVC Multi-Channel Fulfillment Architecture.</p>
    </footer>

</body>
</html>
