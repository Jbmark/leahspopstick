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
     * Fetch all orders with customer and encoder information.
     */
    public function getAllOrders()
    {
        $sql = "
            SELECT
                o.*,
                c.customer_name,
                u.full_name AS encoder_name
            FROM orders o
            JOIN customers c
                ON o.customer_id = c.customer_id
            LEFT JOIN users u
                ON o.encoded_by = u.user_id
            ORDER BY o.created_at DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch one order by ID.
     */
    public function getOrderById($order_id)
    {
        $sql = "
            SELECT
                o.*,
                c.customer_name,
                c.contact_number,
                c.email,
                c.address AS customer_address,
                u.full_name AS encoder_name
            FROM orders o
            JOIN customers c
                ON o.customer_id = c.customer_id
            LEFT JOIN users u
                ON o.encoded_by = u.user_id
            WHERE o.order_id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $order_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all items belonging to an order.
     */
    public function getOrderItems($order_id)
    {
        $sql = "
            SELECT
                od.order_detail_id,
                od.order_id,
                od.product_id,
                od.quantity,
                od.unit_price,
                od.subtotal,
                p.product_name,
                p.unit
            FROM order_details od
            JOIN products p
                ON od.product_id = p.product_id
            WHERE od.order_id = ?
            ORDER BY od.order_detail_id ASC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $order_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new order and its order details.
     */
    public function createOrder(
        $customer_id,
        $order_source,
        $delivery_address,
        $remarks,
        $product_ids,
        $quantities,
        $encoded_by
    ) {
        $allowedSources = [
            'Website',
            'Messenger',
            'Viber',
            'Phone',
            'Email',
            'Walk-in'
        ];

        if (!in_array($order_source, $allowedSources)) {
            throw new Exception(
                "Invalid order source."
            );
        }

        if (empty($product_ids) || empty($quantities)) {
            throw new Exception(
                "Please add at least one product."
            );
        }

        try {

            $this->pdo->beginTransaction();

            /*
             * Validate customer.
             */
            $customerStmt = $this->pdo->prepare("
                SELECT customer_id
                FROM customers
                WHERE customer_id = ?
            ");

            $customerStmt->execute([
                $customer_id
            ]);

            if (!$customerStmt->fetchColumn()) {

                throw new Exception(
                    "Selected customer does not exist."
                );
            }

            /*
             * Create the main order.
             */
            $sql = "
                INSERT INTO orders
                (
                    customer_id,
                    order_source,
                    delivery_address,
                    remarks,
                    status,
                    total_amount,
                    encoded_by
                )
                VALUES (?, ?, ?, ?, 'Pending', 0.00, ?)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $customer_id,
                $order_source,
                $delivery_address,
                $remarks,
                $encoded_by
            ]);

            $order_id = $this->pdo->lastInsertId();

            $total_amount = 0;
            $item_count = 0;

            /*
             * Track products already added to prevent
             * duplicate product lines.
             */
            $addedProducts = [];

            /*
             * Add each product to order_details.
             */
            foreach ($product_ids as $index => $product_id) {

                $product_id = intval($product_id);

                if ($product_id <= 0) {
                    continue;
                }

                $qty = intval(
                    $quantities[$index] ?? 0
                );

                if ($qty <= 0) {
                    continue;
                }

                /*
                 * Prevent duplicate product entries.
                 */
                if (in_array($product_id, $addedProducts)) {

                    throw new Exception(
                        "A product cannot be selected more than once in the same order."
                    );
                }

                /*
                 * Get the active product and current selling price.
                 */
                $pStmt = $this->pdo->prepare("
                    SELECT
                        selling_price
                    FROM products
                    WHERE product_id = ?
                    AND status = 'Active'
                ");

                $pStmt->execute([
                    $product_id
                ]);

                $unit_price = $pStmt->fetchColumn();

                if ($unit_price === false) {

                    throw new Exception(
                        "Selected product does not exist or is inactive."
                    );
                }

                $subtotal = $qty * (float)$unit_price;

                $total_amount += $subtotal;

                $item_count++;

                $dStmt = $this->pdo->prepare("
                    INSERT INTO order_details
                    (
                        order_id,
                        product_id,
                        quantity,
                        unit_price,
                        subtotal
                    )
                    VALUES (?, ?, ?, ?, ?)
                ");

                $dStmt->execute([
                    $order_id,
                    $product_id,
                    $qty,
                    $unit_price,
                    $subtotal
                ]);

                $addedProducts[] = $product_id;
            }

            /*
             * Prevent empty orders.
             */
            if ($item_count === 0) {

                throw new Exception(
                    "Please add at least one valid product with a quantity greater than zero."
                );
            }

            /*
             * Update final order total.
             */
            $uStmt = $this->pdo->prepare("
                UPDATE orders
                SET total_amount = ?
                WHERE order_id = ?
            ");

            $uStmt->execute([
                $total_amount,
                $order_id
            ]);

            $this->pdo->commit();

            return $order_id;

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Update order status.
     *
     * Completed orders receive a completion timestamp.
     * Other statuses clear the completion timestamp.
     */
    public function updateStatus($order_id, $status)
    {
        $allowedStatuses = [
            'Pending',
            'Processing',
            'Completed',
            'Cancelled'
        ];

        if (!in_array($status, $allowedStatuses)) {

            throw new Exception(
                "Invalid order status."
            );
        }

        if ($status === 'Completed') {

            $sql = "
                UPDATE orders
                SET
                    status = ?,
                    completed_at = NOW()
                WHERE order_id = ?
            ";

        } else {

            $sql = "
                UPDATE orders
                SET
                    status = ?,
                    completed_at = NULL
                WHERE order_id = ?
            ";
        }

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $status,
            $order_id
        ]);
    }
}