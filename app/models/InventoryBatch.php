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

    public function getAllBatches()
    {
        $sql = "SELECT ib.*, p.product_name
                FROM inventory_batches ib
                JOIN products p ON ib.product_id = p.product_id
                ORDER BY ib.date_received ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBatch($product_id,$batch_number,$qty,$date_received)
    {
        $sql="INSERT INTO inventory_batches
        (product_id,batch_number,quantity_received,quantity_remaining,date_received)
        VALUES(?,?,?,?,?)";

        $stmt=$this->pdo->prepare($sql);

        return $stmt->execute([
            $product_id,
            $batch_number,
            $qty,
            $qty,
            $date_received
        ]);
    }

    public function getAvailableBatches($product_id)
    {
        $stmt=$this->pdo->prepare("
            SELECT *
            FROM inventory_batches
            WHERE product_id=?
            AND quantity_remaining>0
            ORDER BY date_received ASC
        ");

        $stmt->execute([$product_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentStock()
    {
        $stmt=$this->pdo->query("
            SELECT
                products.product_id,
                products.product_name,
                products.reorder_level,
                products.unit,
                IFNULL(SUM(inventory_batches.quantity_remaining),0) AS current_stock
            FROM products
            LEFT JOIN inventory_batches ON products.product_id=inventory_batches.product_id
            WHERE products.status='Active'
            GROUP BY products.product_id
            ORDER BY products.product_name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
