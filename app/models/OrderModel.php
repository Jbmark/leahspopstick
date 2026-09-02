<?php

require_once __DIR__ . '/../../config/database.php';

class OrderModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Fetch all orders with normalized customer profiles and encoder data
     */
    public function getAllOrders()
    {
        $sql = "SELECT o.*, c.customer_name, u.full_name as encoder_name 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN users u ON o.encoded_by = u.user_id 
                ORDER BY o.created_at DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a distinct order profile by primary key
     */
    public function getOrderById($order_id)
    {
        $sql = "SELECT o.*, c.customer_name, u.full_name as encoder_name 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN users u ON o.encoded_by = u.user_id 
                WHERE o.order_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch item line breakdowns using structural target schema mapping variables
     */
    public function getOrderItems($order_id)
    {
        $sql = "SELECT od.order_detail_id, od.order_id, od.product_id, od.quantity, od.unit_price, od.subtotal, p.product_name, p.unit 
                FROM order_details od 
                JOIN products p ON od.product_id = p.product_id 
                WHERE od.order_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert structural order record data across relational rows safely
     */
    public function createOrder($customer_id, $order_source, $delivery_address, $remarks, $product_ids, $quantities, $encoded_by)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO orders (customer_id, order_source, delivery_address, remarks, status, total_amount, encoded_by) 
                    VALUES (?, ?, ?, ?, 'Pending', 0.00, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$customer_id, $order_source, $delivery_address, $remarks, $encoded_by]);
            $order_id = $this->pdo->lastInsertId();

            $total_amount = 0;

            foreach ($product_ids as $index => $product_id) {
                if (empty($product_id)) continue;
                
                $qty = intval($quantities[$index]);
                if ($qty <= 0) continue;

                $pStmt = $this->pdo->prepare("SELECT selling_price FROM products WHERE product_id = ?");
                $pStmt->execute([$product_id]);
                $unit_price = $pStmt->fetchColumn();

                // 🌟 FIX 1: Input Validation Guard to prevent invalid product entries
                if ($unit_price === false) {
                    throw new Exception("Selected product does not exist.");
                }

                $subtotal = $qty * $unit_price;
                $total_amount += $subtotal;

                $dStmt = $this->pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $dStmt->execute([$order_id, $product_id, $qty, $unit_price, $subtotal]);
            }

            $uStmt = $this->pdo->prepare("UPDATE orders SET total_amount = ? WHERE order_id = ?");
            $uStmt->execute([$total_amount, $order_id]);

            $this->pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Update order state status and toggle completion timestamps cleanly
     */
    public function updateStatus($order_id, $status)
    {
        // 🌟 FIX 2: Explicitly toggle completed_at to NOW() or NULL depending on state changes
        if ($status === 'Completed') {
            $sql = "UPDATE orders 
                    SET status = ?, completed_at = NOW() 
                    WHERE order_id = ?";
        } else {
            $sql = "UPDATE orders 
                    SET status = ?, completed_at = NULL 
                    WHERE order_id = ?";
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $order_id]);
    }
}
