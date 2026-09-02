<?php

require_once __DIR__.'/../../config/database.php';

class InventoryTransaction
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo=$pdo;
    }

    public function record($product,$batch,$type,$qty,$reference,$user,$remarks='')
    {
        $sql="INSERT INTO inventory_transactions
        (product_id,batch_id,transaction_type,quantity,reference_id,created_by,remarks)
        VALUES(?,?,?,?,?,?,?)";

        $stmt=$this->pdo->prepare($sql);

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
