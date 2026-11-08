<?php

require_once __DIR__.'/../models/InventoryBatch.php';
require_once __DIR__.'/../models/InventoryTransaction.php';
require_once __DIR__.'/../../config/database.php';

class InventoryController
{
    private $batchModel;
    private $transactionModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->batchModel = new InventoryBatch();
        $this->transactionModel = new InventoryTransaction();
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {

            /*
            |--------------------------------------------------------------------------
            | ADD INVENTORY BATCH
            |--------------------------------------------------------------------------
            */
            
case 'add_batch':

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        try {

            $product_id = filter_var(
                $_POST['product_id'] ?? null,
                FILTER_VALIDATE_INT
            );

            $rawQuantity = $_POST['quantity'] ?? '';
            $date_received = trim($_POST['date_received'] ?? '');

            if (!$product_id || $product_id <= 0) {
                throw new Exception("Please select a product.");
            }

            if (
                !is_scalar($rawQuantity) ||
                !preg_match('/^[1-9][0-9]*$/', (string)$rawQuantity)
            ) {
                throw new Exception(
                    "Quantity must be a whole number greater than zero."
                );
            }

            $quantity = (int)$rawQuantity;

            if ($date_received === '') {
                throw new Exception("Please enter the date received.");
            }

            $date = DateTime::createFromFormat('!Y-m-d', $date_received);

            if (
                !$date ||
                $date->format('Y-m-d') !== $date_received
            ) {
                throw new Exception("Please enter a valid received date.");
            }

            // Confirm the selected product is active.
           
$productStmt = $this->pdo->prepare("
    SELECT product_id, product_name
    FROM products
    WHERE product_id = ?
      AND status = 'Active'
");

$productStmt->execute([$product_id]);
$product = $productStmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    throw new Exception(
        "The selected product is unavailable."
    );
}

// Assign a short code to each main product.
$productCodes = [
    'Coffee Stirrer'  => 'CS',
    'Ice Cream Spoon' => 'ICS',
    'Popsicle Stick'  => 'PS',
    'Spork'           => 'SP',
    'Wooden Fork'     => 'WF',
    'Wooden Spoon'    => 'WS'
];

$productName = $product['product_name'];

if (!isset($productCodes[$productName])) {
    throw new Exception(
        "No batch code is configured for this product: "
        . $productName
    );
}

$productCode = $productCodes[$productName];

            /*
             * Save the batch and Stock In transaction together.
             */
            $this->pdo->beginTransaction();
           
/*
 * Generate a product-specific batch number.
 * Example: CS-20261017-001
 *
 * The sequence restarts daily for each product.
 */
$receivedDate = $date->format('Ymd');

$prefix = $productCode . '-' . $receivedDate . '-';

$stmt = $this->pdo->prepare("
    SELECT batch_number
    FROM inventory_batches
    WHERE batch_number LIKE ?
    FOR UPDATE
");

$stmt->execute([$prefix . '%']);

$highestNumber = 0;

foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $existingNumber) {

    if (
        preg_match(
            '/^' . preg_quote($prefix, '/') . '([0-9]+)$/',
            $existingNumber,
            $matches
        )
    ) {
        $highestNumber = max(
            $highestNumber,
            (int)$matches[1]
        );
    }
}

$batch_number = $prefix .
    str_pad(
        (string)($highestNumber + 1),
        3,
        '0',
        STR_PAD_LEFT
    );

            // Create the inventory batch.
            $this->batchModel->addBatch(
                $product_id,
                $batch_number,
                $quantity,
                $date_received
            );

            $batch_id = (int)$this->pdo->lastInsertId();

            if ($batch_id <= 0) {
                throw new Exception(
                    "Unable to retrieve the new batch ID."
                );
            }

            $user_id = $_SESSION['user']['user_id'] ?? 1;

            // Record Stock In for this batch.
            $this->transactionModel->record(
                $product_id,
                $batch_id,
                'Stock In',
                $quantity,
                null,
                $user_id,
                'Initial batch entry'
            );

            // Commit both operations.
            $this->pdo->commit();

            $_SESSION['success'] =
                "Inventory batch {$batch_number} added successfully.";

           // Open the saved batch's QR code page.
header(
    "Location: inventory.php?action=view_batch&id="
    . $batch_id
);
exit;

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $_SESSION['inventory_error'] = $e instanceof Exception
                ? $e->getMessage()
                : "Unable to save the inventory batch. Please try again.";

            header("Location: inventory.php?action=add_batch");
            exit;
        }
    }

    $products = $this->pdo->query("
        SELECT *
        FROM products
        WHERE status = 'Active'
        ORDER BY product_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    include 'app/views/inventory/add_batch.php';

    break;
       
    
case 'scan_qr':

    include 'app/views/inventory/scan_qr.php';

    break;            
                
                

case 'view_batch':

    $batch_id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$batch_id || $batch_id <= 0) {
        $_SESSION['inventory_error'] = "Invalid batch ID.";
        header("Location: inventory.php");
        exit;
    }

    // Get batch details
    $stmt = $this->pdo->prepare("
        SELECT
            ib.batch_id,
            ib.batch_number,
            ib.product_id,
            ib.quantity_received,
            ib.quantity_remaining,
            ib.date_received,
            ib.status,
            p.product_name,
            p.unit
        FROM inventory_batches ib
        JOIN products p
            ON p.product_id = ib.product_id
        WHERE ib.batch_id = ?
    ");

    $stmt->execute([$batch_id]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$batch) {
        $_SESSION['inventory_error'] = "Batch not found.";
        header("Location: inventory.php");
        exit;
    }

    // Get transaction history for this batch
    $historyStmt = $this->pdo->prepare("
        SELECT
            transaction_id,
            transaction_type,
            quantity,
            reference_id,
            transaction_date,
            remarks,
            created_by
        FROM inventory_transactions
        WHERE batch_id = ?
        ORDER BY transaction_date DESC, transaction_id DESC
    ");

    $historyStmt->execute([$batch_id]);
    $batchHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

    // Display batch details and history
    include 'app/views/inventory/batch_qr.php';

    break;
     

            /*
            |--------------------------------------------------------------------------
            | DEFAULT INVENTORY DASHBOARD
            |--------------------------------------------------------------------------
            */
            default:

                $stocks = $this->batchModel->getCurrentStock();

                $batches = $this->batchModel->getAllBatches();

                include 'app/views/inventory/index.php';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FIFO DEDUCTION
    |--------------------------------------------------------------------------
    |
    | This method deducts inventory from the oldest available batches first.
    |
    */
    public function deductFIFO(
        $product_id,
        $qty,
        $order_id,
        $user_id
    ) {
        $remaining = $qty;

        $batches = $this->batchModel->getAvailableBatches($product_id);

        foreach ($batches as $batch) {

            if ($remaining <= 0) {
                break;
            }

            $available = (float)$batch['quantity_remaining'];

            $deduct = min($remaining, $available);

            $newRemaining = $available - $deduct;

            $stmt = $this->pdo->prepare("
                UPDATE inventory_batches
                SET
                    quantity_remaining = ?,
                    status = CASE
                        WHEN ? <= 0 THEN 'Depleted'
                        ELSE 'Available'
                    END
                WHERE batch_id = ?
            ");

            $stmt->execute([
                $newRemaining,
                $newRemaining,
                $batch['batch_id']
            ]);

            $this->transactionModel->record(
                $product_id,
                $batch['batch_id'],
                'Stock Out',
                $deduct,
                $order_id,
                $user_id,
                'FIFO deduction'
            );

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new Exception("Not enough stock.");
        }
    }



/*
|--------------------------------------------------------------------------
| COMPLETE ORDER USING FIFO + STOCK SNAPSHOTS
|--------------------------------------------------------------------------
*/
public function consumeFIFO($order_id)
{
    global $pdo;

    try {
    $pdo->beginTransaction();

    /*
     * Lock the order and verify its current status.
     * This helps prevent duplicate completion attempts.
     */
    $orderStmt = $pdo->prepare("
        SELECT status
        FROM orders
        WHERE order_id = ?
        FOR UPDATE
    ");

    $orderStmt->execute([$order_id]);
    $orderStatus = $orderStmt->fetchColumn();

    if ($orderStatus === false) {
        throw new Exception("Order not found.");
    }

    if ($orderStatus !== 'Processing') {
        throw new Exception(
            "Only Processing orders can be completed."
        );
    }

    /*
     * Get all products and quantities in this order.
     */
        $stmt = $pdo->prepare("
            SELECT product_id, SUM(quantity) AS quantity
            FROM order_details
            WHERE order_id = ?
            GROUP BY product_id
        ");

        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            throw new Exception(
                "This order has no valid items."
            );
        }

        /*
         * Prepare snapshot insert.
         */
        $snapshotStmt = $pdo->prepare("
            INSERT INTO order_stock_snapshots
            (
                order_id,
                product_id,
                quantity_ordered,
                stock_before,
                stock_after,
                recorded_at
            )
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        /*
         * Process every product in the order.
         */
        foreach ($items as $item) {

            $product_id = (int)$item['product_id'];
            $needed = (int)$item['quantity'];
            $quantity_ordered = $needed;

            /*
             * Get total stock across all batches
             * before deduction.
             */
            $stockStmt = $pdo->prepare("
                SELECT COALESCE(SUM(quantity_remaining), 0)
                FROM inventory_batches
                WHERE product_id = ?
            ");

            $stockStmt->execute([$product_id]);
            $stock_before = (int)$stockStmt->fetchColumn();

            /*
             * Get available FIFO batches:
             * oldest batch first.
             */
            $batchStmt = $pdo->prepare("
                SELECT *
                FROM inventory_batches
                WHERE product_id = ?
                  AND quantity_remaining > 0
                  AND status = 'Available'
                ORDER BY date_received ASC, batch_id ASC
            ");

            $batchStmt->execute([$product_id]);

            while ($needed > 0) {

                $batch = $batchStmt->fetch(PDO::FETCH_ASSOC);

                if (!$batch) {
                    throw new Exception(
                        "Insufficient warehouse inventory for Product ID: "
                        . $product_id
                    );
                }

                $available =
                    (int)$batch['quantity_remaining'];

                $consume = min($needed, $available);
                $newRemaining = $available - $consume;

                /*
                 * Update the FIFO batch.
                 */
                $update = $pdo->prepare("
                    UPDATE inventory_batches
                    SET quantity_remaining = ?,
                        status = CASE
                            WHEN ? <= 0 THEN 'Depleted'
                            ELSE 'Available'
                        END
                    WHERE batch_id = ?
                ");

                $update->execute([
                    $newRemaining,
                    $newRemaining,
                    $batch['batch_id']
                ]);

                /*
                 * Record the Stock Out transaction.
                 */
                $log = $pdo->prepare("
                    INSERT INTO inventory_transactions
                    (
                        product_id,
                        batch_id,
                        transaction_type,
                        quantity,
                        reference_id,
                        remarks,
                        created_by
                    )
                    VALUES (
                        ?, ?, 'Stock Out', ?, ?,
                        'FIFO deduction after completed order',
                        ?
                    )
                ");

                $log->execute([
                    $product_id,
                    $batch['batch_id'],
                    $consume,
                    $order_id,
                    $_SESSION['user']['user_id'] ?? 1
                ]);

                $needed -= $consume;
            }

            /*
             * Get total stock after all FIFO deductions
             * for this product.
             */
            $stockStmt->execute([$product_id]);
            $stock_after = (int)$stockStmt->fetchColumn();

            /*
             * Save the before-and-after snapshot.
             */
            $snapshotStmt->execute([
                $order_id,
                $product_id,
                $quantity_ordered,
                $stock_before,
                $stock_after
            ]);
        }

        /*
 * Mark the order Completed in the same transaction
 * as the FIFO deductions and stock snapshots.
 */
$statusStmt = $pdo->prepare("
    UPDATE orders
    SET status = 'Completed',
        completed_at = NOW()
    WHERE order_id = ?
      AND status = 'Processing'
");

$statusStmt->execute([$order_id]);

if ($statusStmt->rowCount() !== 1) {
    throw new Exception(
        "Unable to complete the order. Please try again."
    );
}

/*
 * Commit inventory deductions, snapshots, and
 * order status together.
 */
$pdo->commit();

return true;
        
    } catch (Exception $e) {

        /*
         * Roll back both inventory changes and
         * snapshots if anything fails.
         */
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


  
/**
 * Cancel an order and restore inventory atomically.
 *
 * Completed orders have their FIFO-deducted inventory restored.
 * Pending and Processing orders are cancelled without restoration.
 *
 * @param int $order_id
 * @return string The order's original status
 * @throws Exception If cancellation cannot be completed safely
 */
public function cancelOrderAndRestoreInventory($order_id)
{
    global $pdo;

    try {
        $pdo->beginTransaction();

        /*
         * Lock the order first so two cancellation requests
         * cannot restore the same order concurrently.
         */
        $orderStmt = $pdo->prepare("
            SELECT order_id, status
            FROM orders
            WHERE order_id = ?
            FOR UPDATE
        ");

        $orderStmt->execute([$order_id]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Order not found.");
        }

        $originalStatus = $order['status'];

        if ($originalStatus === 'Cancelled') {
            throw new Exception(
                "This order has already been cancelled."
            );
        }

        if (!in_array(
            $originalStatus,
            ['Pending', 'Processing', 'Completed'],
            true
        )) {
            throw new Exception("Invalid order status.");
        }

        /*
         * Only Completed orders should have FIFO stock deductions.
         */
        if ($originalStatus === 'Completed') {

            /*
             * Safety check: never restore an order that already
             * has Return records. Stop instead of risking a
             * duplicate restoration.
             */
            $returnCheck = $pdo->prepare("
                SELECT COUNT(*)
                FROM inventory_transactions
                WHERE reference_id = ?
                  AND transaction_type = 'Return'
            ");

            $returnCheck->execute([$order_id]);

            if ((int) $returnCheck->fetchColumn() > 0) {
                throw new Exception(
                    "This completed order already has Return "
                    . "transactions. Inventory was not changed. "
                    . "Please inspect the records before proceeding."
                );
            }

            /*
             * Retrieve the original FIFO Stock Out records.
             */
            $logs = $pdo->prepare("
                SELECT
                    transaction_id,
                    product_id,
                    batch_id,
                    quantity
                FROM inventory_transactions
                WHERE reference_id = ?
                  AND transaction_type = 'Stock Out'
                ORDER BY transaction_id DESC
                FOR UPDATE
            ");

            $logs->execute([$order_id]);
            $stockOutRows = $logs->fetchAll(PDO::FETCH_ASSOC);

            if (empty($stockOutRows)) {
                throw new Exception(
                    "No FIFO inventory transactions were found "
                    . "for this completed order."
                );
            }

            $restore = $pdo->prepare("
                UPDATE inventory_batches
                SET
                    quantity_remaining = quantity_remaining + ?,
                    status = 'Available'
                WHERE batch_id = ?
                  AND product_id = ?
            ");

            $returnLog = $pdo->prepare("
                INSERT INTO inventory_transactions (
                    product_id,
                    batch_id,
                    transaction_type,
                    quantity,
                    reference_id,
                    remarks,
                    created_by
                )
                VALUES (?, ?, 'Return', ?, ?, ?, ?)
            ");

            $userId = $_SESSION['user']['user_id'] ?? 1;

            foreach ($stockOutRows as $row) {

                if (
                    $row['batch_id'] === null ||
                    (int) $row['quantity'] <= 0
                ) {
                    throw new Exception(
                        "Invalid Stock Out record. "
                        . "Inventory was not changed."
                    );
                }

                /*
                 * Return stock to its original FIFO batch.
                 */
                $restore->execute([
                    $row['quantity'],
                    $row['batch_id'],
                    $row['product_id']
                ]);

                if ($restore->rowCount() !== 1) {
                    throw new Exception(
                        "The original inventory batch could not "
                        . "be restored. Please check batch "
                        . $row['batch_id'] . "."
                    );
                }

                /*
                 * Record the restoration in the audit trail.
                 */
                $returnLog->execute([
                    $row['product_id'],
                    $row['batch_id'],
                    $row['quantity'],
                    $order_id,
                    'Inventory restored after order cancellation',
                    $userId
                ]);
            }
        }

        /*
         * Change the status inside the same transaction.
         * If this fails, the inventory restoration rolls back too.
         */
        $updateOrder = $pdo->prepare("
            UPDATE orders
            SET
                status = 'Cancelled',
                completed_at = NULL
            WHERE order_id = ?
              AND status = ?
        ");

        $updateOrder->execute([
            $order_id,
            $originalStatus
        ]);

        if ($updateOrder->rowCount() !== 1) {
            throw new Exception(
                "Order status could not be updated. "
                . "The operation has been rolled back."
            );
        }

        $pdo->commit();

        return $originalStatus;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
}