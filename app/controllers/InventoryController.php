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

        $this->pdo=$pdo;
        $this->batchModel=new InventoryBatch();
        $this->transactionModel=new InventoryTransaction();
    }

    public function handleRequest()
    {
        $action=$_GET['action'] ?? 'index';

        switch($action){
            case 'add_batch':
                if($_SERVER['REQUEST_METHOD']=='POST'){
                    $this->batchModel->addBatch(
                        $_POST['product_id'],
                        $_POST['batch_number'],
                        $_POST['quantity'],
                        $_POST['date_received']
                    );
                    
                    $batch_id = $this->pdo->lastInsertId();
                    $user_id = $_SESSION['user']['user_id'] ?? 1;
                    
                    $this->transactionModel->record(
                        $_POST['product_id'],
                        $batch_id,
                        'Stock In',
                        $_POST['quantity'],
                        null,
                        $user_id,
                        'Initial batch entry'
                    );

                    header("Location: inventory.php");
                    exit;
                }

                $products = $this->pdo->query("SELECT * FROM products WHERE status='Active'")->fetchAll(PDO::FETCH_ASSOC);
                include 'app/views/inventory/add_batch.php';
                break;

            default:
                $stocks = $this->batchModel->getCurrentStock();
                $batches = $this->batchModel->getAllBatches();
                include 'app/views/inventory/index.php';
        }
    }

    public function deductFIFO($product_id,$qty,$order_id,$user_id)
    {
        $remaining=$qty;

        $batches=$this->batchModel->getAvailableBatches($product_id);

        foreach($batches as $batch)
        {
            if($remaining<=0)
                break;

            $available=$batch['quantity_remaining'];

            $deduct=min($remaining,$available);

            $stmt=$this->pdo->prepare("
            UPDATE inventory_batches
            SET quantity_remaining=quantity_remaining-?
            WHERE batch_id=?");

            $stmt->execute([$deduct,$batch['batch_id']]);

            $this->transactionModel->record(
                $product_id,
                $batch['batch_id'],
                'Stock Out',
                $deduct,
                $order_id,
                $user_id,
                'FIFO deduction'
            );

            $remaining-=$deduct;
        }

        if($remaining>0)
        {
            throw new Exception("Not enough stock.");
        }
    }

        /**
     * Consume stock using FIFO (oldest batch first)
     * Directly matches Chapter 3 algorithmic workflow specifications
     */
    public function consumeFIFO($order_id)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // 1. Gather all line items registered within this order
            $stmt = $pdo->prepare("
                SELECT product_id, quantity
                FROM order_details
                WHERE order_id = ?
            ");
            $stmt->execute([$order_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($items as $item){
                $product_id = $item['product_id'];
                $needed = $item['quantity'];

                // 2. Fetch oldest available inventory batch structures first
                $batchStmt = $pdo->prepare("
                    SELECT *
                    FROM inventory_batches
                    WHERE product_id = ?
                    AND quantity_remaining > 0
                    ORDER BY date_received ASC
                ");
                $batchStmt->execute([$product_id]);

                // 3. Chronological algorithm consumption loop
                while($needed > 0){
                    $batch = $batchStmt->fetch(PDO::FETCH_ASSOC);

                    if(!$batch){
                        throw new Exception("Insufficient warehouse inventory for Product ID: " . $product_id);
                    }

                    $consume = min($needed, $batch['quantity_remaining']);
                    $newRemaining = $batch['quantity_remaining'] - $consume;

                    // Update specific inventory batch node balances
                    $update = $pdo->prepare("
                        UPDATE inventory_batches
                        SET quantity_remaining = ?
                        WHERE batch_id = ?
                    ");
                    $update->execute([$newRemaining, $batch['batch_id']]);

                    // Inject chronological trace record directly into transactions log ledger
                    $log = $pdo->prepare("
                        INSERT INTO inventory_transactions
                        (product_id, batch_id, transaction_type, quantity, reference_id, remarks, created_by)
                        VALUES (?, ?, 'Stock Out', ?, ?, 'FIFO deduction after completed order', ?)
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

            $pdo->commit();
            return true;

        } catch(Exception $e){
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Restore stock after cancelled order (Symmetrical Rollback Engine)
     */
    public function restoreOrderInventory($order_id)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // 1. Fetch exact chronological logs generated during initial consumption execution
            $logs = $pdo->prepare("
                SELECT *
                FROM inventory_transactions
                WHERE reference_id = ?
                AND transaction_type = 'Stock Out'
                ORDER BY transaction_id DESC
            ");
            $logs->execute([$order_id]);

            // 2. Run symmetrical re-credit loops back into original batch records
            while($row = $logs->fetch(PDO::FETCH_ASSOC)){
                $pdo->prepare("
                    UPDATE inventory_batches
                    SET quantity_remaining = quantity_remaining + ?
                    WHERE batch_id = ?
                ")->execute([
                    $row['quantity'],
                    $row['batch_id']
                ]);

                // Create matching auditable reverse trail record entry
                $pdo->prepare("
                    INSERT INTO inventory_transactions
                    (product_id, batch_id, transaction_type, quantity, reference_id, remarks, created_by)
                    VALUES (?, ?, 'Return', ?, ?, 'Inventory restored after order cancellation', ?)
                ")->execute([
                    $row['product_id'],
                    $row['batch_id'],
                    $row['quantity'],
                    $order_id,
                    $_SESSION['user']['user_id'] ?? 1
                ]);
            }

            $pdo->commit();
            return true;

        } catch(Exception $e){
            $pdo->rollBack();
            throw $e;
        }
    }
}
