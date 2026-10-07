<?php

require_once __DIR__.'/../../config/database.php';

class InventoryTransaction
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Record an inventory transaction.
     *
     * Transaction types:
     * Stock In
     * Stock Out
     * Adjustment
     * Return
     */
    public function record(
        $product,
        $batch,
        $type,
        $qty,
        $reference,
        $user,
        $remarks = ''
    ) {
        $allowedTypes = [
            'Stock In',
            'Stock Out',
            'Adjustment',
            'Return'
        ];

        $qty = (float)$qty;

        if ($qty <= 0) {
            throw new Exception(
                "Inventory transaction quantity must be greater than zero."
            );
        }

        if (!in_array($type, $allowedTypes)) {
            throw new Exception(
                "Invalid inventory transaction type."
            );
        }

        $sql = "
            INSERT INTO inventory_transactions
            (
                product_id,
                batch_id,
                transaction_type,
                quantity,
                reference_id,
                created_by,
                remarks
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $product,
            $batch,
            $type,
            $qty,
            $reference,
            $user,
            $remarks
        ]);
    }
}