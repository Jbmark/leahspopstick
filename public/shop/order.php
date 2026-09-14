<?php
require_once '../../config/database.php';
require_once '../../app/models/OrderModel.php';

$orderModel = new OrderModel();

// Fetch active products dynamically from your database to fill up the storefront form matrix rows
$products = $pdo->query("SELECT * FROM products WHERE status='Active' ORDER BY product_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch a default dynamic customer account to comply with your normalized DB structure constraints
// (If a guest checks out, they default map onto your seeded corporate retail placeholder node id 1)
$default_customer_id = 1; 

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect frontend values safely
    $customer_name = trim($_POST['customer_name']);
    $delivery_address = trim($_POST['delivery_address']);
    $remarks = trim($_POST['remarks']) . " [Web Guest Name: " . $customer_name . "]"; // Preserves guest name in remarks text space
    
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    // Double check that at least one item was checked with a positive quantity count
    $has_items = false;
    foreach ($product_ids as $index => $prod_id) {
        if (!empty($prod_id) && intval($quantities[$index]) > 0) {
            $has_items = true;
            break;
        }
    }

    if (!$has_items) {
        $error_message = "Validation Exception: Please select at least one finished product with a valid numeric quantity volume.";
    } else {
        try {
            // Trigger your standardized backend transaction method model block
            // Business Rule Sync: sets source channel to 'Website' and encoded_by to NULL
            $order_id = $orderModel->createOrder(
                $default_customer_id,
                'Website',
                $delivery_address,
                $remarks,
                $product_ids,
                $quantities,
                null // encoded_by = NULL (Automated Checkout)
            );

            // Redirect smoothly to a clean confirmation screen state passing the track token ID
            header("Location: track.php?id=" . $order_id . "&success=1");
            exit;

        } catch (Exception $e) {
            $error_message = "Checkout Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order Online - Leah's Popstick</title>
    <link rel="stylesheet" href="../css/shop.css">
    <style>
        .form-section { padding: 60px 10%; background: #FAFAFA; min-height: 80vh; display: flex; justify-content: center; }
        .form-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); width: 100%; max-width: 700px; border: 1px solid #E5E9F0; }
        .form-card h2 { color: #1B5E20; margin-bottom: 8px; font-size: 24px; }
        .form-card p { color: #7A869A; font-size: 14px; margin-bottom: 25px; }
        .field-group { margin-bottom: 20px; }
        .field-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .field-group input, .field-group textarea, .field-group select { width: 100%; padding: 12px 16px; border: 1px solid #E0E0E0; border-radius: 8px; font-size: 15px; background: #FAFAFA; transition: all 0.3s ease; }
        .field-group input:focus, .field-group textarea:focus, .field-group select:focus { outline: none; border-color: #2E7D32; background: white; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
        .item-row { display: grid; grid-template-columns: 3fr 1fr; gap: 16px; margin-bottom: 12px; }
        .alert-box { padding: 14px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
        .alert-error { background: #FFEBEE; color: #C62828; border-left: 4px solid #D32F2F; }
        .btn-submit { width: 100%; padding: 14px; background: #1B5E20; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; margin-top: 10px; box-shadow: 0 4px 12px rgba(27,94,32,0.2); }
        .btn-submit:hover { background: #2E7D32; }
    </style>
</head>
<body>

<section class="form-section">
    <div class="form-card">
        <h2>Online Purchase Order Checkout</h2>
        <p>Submit your logistics requests directly into our centralized manufacturing fulfillment pipeline.</p>

        <?php if (!empty($error_message)) { ?>
            <div class="alert-box alert-error"><?= htmlspecialchars($error_message); ?></div>
        <?php } ?>

        <form method="POST">
            <!-- Guest Profile Fields Blocks -->
            <div class="field-group">
                <label>Contact Full Name / Business Entity</label>
                <input type="text" name="customer_name" required placeholder="e.g., Juan Dela Cruz / ABC Ice Drop Co.">
            </div>

            <div class="field-group">
                <label>Complete Shipping Delivery Address</label>
                <textarea name="delivery_address" required style="height: 70px;" placeholder="Provide complete street, warehouse, and municipality destination data..."></textarea>
            </div>

            <div class="field-group">
                <label>Order Special Notes / Remarks</label>
                <textarea name="remarks" style="height: 50px;" placeholder="Optional packaging request context parameters..."></textarea>
            </div>

            <hr style="border: 0; border-top: 1px solid #E5E9F0; margin: 25px 0;">
            <h3 style="margin-bottom: 12px; color: #1B5E20; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Product Purchase Selection Matrix</h3>

            <!-- Multiline Array Entry Parameters Row Elements Loop -->
            <div id="checkout-matrix-container">
                <?php for ($loop = 0; $loop < 3; $loop++) { ?>
                <div class="item-row">
                    <select name="product_id[]">
                        <option value="">-- Choose Eco-Utensil Finished Product --</option>
                        <?php foreach ($products as $p) { ?>
                            <option value="<?= $p['product_id']; ?>">
                                <?= htmlspecialchars($p['product_name']); ?> [₱<?= number_format($p['selling_price'], 2); ?> per <?= htmlspecialchars($p['unit']); ?>]
                            </option>
                        <?php } ?>
                    </select>
                    <input type="number" name="quantity[]" min="1" placeholder="Qty">
                </div>
                <?php } ?>
            </div>

            <button type="submit" class="btn-submit">Commit Web Checkout Request</button>
        </form>
    </div>
</section>

<footer>
    <p>© <?= date('Y'); ?> Leah's Popstick Wood Products Manufacturing</p>
</footer>

</body>
</html>
