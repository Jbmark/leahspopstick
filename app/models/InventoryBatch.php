<?php

require_once __DIR__ . '/../../config/database.php';

class InventoryBatch
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Get all inventory batches.
     * Oldest batches appear first for easy FIFO monitoring.
     */
    public function getAllBatches()
    {
        $sql = "
            SELECT
                ib.*,
                p.product_name
            FROM inventory_batches ib
            JOIN products p
                ON ib.product_id = p.product_id
            ORDER BY ib.date_received ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new inventory batch.
     */
    public function addBatch(
        $product_id,
        $batch_number,
        $qty,
        $date_received
    ) {
        $qty = (float)$qty;

        if ($qty <= 0) {
            throw new Exception(
                "Quantity received must be greater than zero."
            );
        }

        $sql = "
            INSERT INTO inventory_batches
            (
                product_id,
                batch_number,
                quantity_received,
                quantity_remaining,
                date_received,
                status
            )
            VALUES (?, ?, ?, ?, ?, 'Available')
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $product_id,
            $batch_number,
            $qty,
            $qty,
            $date_received
        ]);
    }

    /**
     * Get available batches for FIFO deduction.
     *
     * Oldest date_received is consumed first.
     */
    public function getAvailableBatches($product_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM inventory_batches
            WHERE product_id = ?
            AND quantity_remaining > 0
            AND status = 'Available'
            ORDER BY date_received ASC, batch_id ASC
        ");

        $stmt->execute([
            $product_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get current stock per active finished product.
     */
    public function getCurrentStock()
    {
        $stmt = $this->pdo->query("
            SELECT
                products.product_id,
                products.product_name,
                products.reorder_level,
                products.unit,
                IFNULL(
                    SUM(inventory_batches.quantity_remaining),
                    0
                ) AS current_stock
            FROM products
            LEFT JOIN inventory_batches
                ON products.product_id = inventory_batches.product_id
            WHERE products.status = 'Active'
            GROUP BY
                products.product_id,
                products.product_name,
                products.reorder_level,
                products.unit
            ORDER BY products.product_name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}