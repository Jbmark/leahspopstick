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

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    try {

                        $product_id = intval($_POST['product_id'] ?? 0);
                        $batch_number = trim($_POST['batch_number'] ?? '');
                        $quantity = (float)($_POST['quantity'] ?? 0);
                        $date_received = $_POST['date_received'] ?? '';

                        if ($product_id <= 0) {
                            throw new Exception("Please select a product.");
                        }

                        if ($batch_number === '') {
                            throw new Exception("Please enter a batch number.");
                        }

                        if ($quantity <= 0) {
                            throw new Exception("Quantity must be greater than zero.");
                        }

                        if ($date_received === '') {
                            throw new Exception("Please enter the date received.");
                        }

                        $this->batchModel->addBatch(
                            $product_id,
                            $batch_number,
                            $quantity,
                            $date_received
                        );

                        $batch_id = $this->pdo->lastInsertId();

                        $user_id = $_SESSION['user']['user_id'] ?? 1;

                        $this->transactionModel->record(
                            $product_id,
                            $batch_id,
                            'Stock In',
                            $quantity,
                            null,
                            $user_id,
                            'Initial batch entry'
                        );

                        $_SESSION['success'] =
                            "Inventory batch added successfully.";

                        header("Location: inventory.php");
                        exit;

                    } catch (Exception $e) {

                        $_SESSION['inventory_error'] =
                            $e->getMessage();

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
    | COMPLETE ORDER USING FIFO
    |--------------------------------------------------------------------------
    */
    public function consumeFIFO($order_id)
    {
        global $pdo;

        try {

            $pdo->beginTransaction();

            /*
             * Get all products and quantities
             * belonging to the order.
             */
            $stmt = $pdo->prepare("
                SELECT
                    product_id,
                    quantity
                FROM order_details
                WHERE order_id = ?
            ");

            $stmt->execute([
                $order_id
            ]);

            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($items)) {
                throw new Exception(
                    "This order has no valid items."
                );
            }

            /*
             * Process every product in the order.
             */
            foreach ($items as $item) {

                $product_id = $item['product_id'];
                $needed = (float)$item['quantity'];

                /*
                 * Get available batches from oldest
                 * to newest.
                 */
                $batchStmt = $pdo->prepare("
                    SELECT *
                    FROM inventory_batches
                    WHERE product_id = ?
                    AND quantity_remaining > 0
                    AND status = 'Available'
                    ORDER BY date_received ASC, batch_id ASC
                ");

                $batchStmt->execute([
                    $product_id
                ]);

                while ($needed > 0) {

                    $batch = $batchStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$batch) {

                        throw new Exception(
                            "Insufficient warehouse inventory for Product ID: "
                            . $product_id
                        );
                    }

                    $available =
                        (float)$batch['quantity_remaining'];

                    $consume = min(
                        $needed,
                        $available
                    );

                    $newRemaining =
                        $available - $consume;

                    /*
                     * Deduct from the current FIFO batch
                     * and update its status.
                     */
                    $update = $pdo->prepare("
                        UPDATE inventory_batches
                        SET
                            quantity_remaining = ?,
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
                        VALUES
                        (
                            ?,
                            ?,
                            'Stock Out',
                            ?,
                            ?,
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
            }

            /*
             * Everything succeeded.
             */
            $pdo->commit();

            return true;

        } catch (Exception $e) {

            /*
             * If anything fails, undo every inventory
             * change made during this FIFO operation.
             */
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE INVENTORY AFTER ORDER CANCELLATION
    |--------------------------------------------------------------------------
    */
    public function restoreOrderInventory($order_id)
    {
        global $pdo;

        try {

            $pdo->beginTransaction();

            /*
             * Find the original Stock Out transactions
             * created for this order.
             */
            $logs = $pdo->prepare("
                SELECT *
                FROM inventory_transactions
                WHERE reference_id = ?
                AND transaction_type = 'Stock Out'
                ORDER BY transaction_id DESC
            ");

            $logs->execute([
                $order_id
            ]);

            $hasLogs = false;

            while ($row = $logs->fetch(PDO::FETCH_ASSOC)) {

                $hasLogs = true;

                /*
                 * Return the quantity to the original batch.
                 */
                $restore = $pdo->prepare("
                    UPDATE inventory_batches
                    SET
                        quantity_remaining =
                            quantity_remaining + ?,
                        status = 'Available'
                    WHERE batch_id = ?
                ");

                $restore->execute([
                    $row['quantity'],
                    $row['batch_id']
                ]);

                /*
                 * Record the Return transaction.
                 */
                $returnLog = $pdo->prepare("
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
                    VALUES
                    (
                        ?,
                        ?,
                        'Return',
                        ?,
                        ?,
                        'Inventory restored after order cancellation',
                        ?
                    )
                ");

                $returnLog->execute([
                    $row['product_id'],
                    $row['batch_id'],
                    $row['quantity'],
                    $order_id,
                    $_SESSION['user']['user_id'] ?? 1
                ]);
            }

            /*
             * A completed order should normally have
             * Stock Out records.
             */
            if (!$hasLogs) {
                throw new Exception(
                    "No FIFO inventory transactions were found for this completed order."
                );
            }

            $pdo->commit();

            return true;

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }
}
