<?php

require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/InventoryController.php';
require_once __DIR__ . '/../../config/database.php';

class OrderController
{
    private $orderModel;
    private $inventoryController;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->orderModel = new OrderModel();
        $this->inventoryController = new InventoryController();
    }

    private function checkPermission($allowedRoles)
    {
        $role = $_SESSION['user']['role_name'] ?? '';

        if (!in_array($role, $allowedRoles)) {
            die("Unauthorized Operation: Your access profile restricts this action.");
        }
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {

            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER
            |--------------------------------------------------------------------------
            */
            case 'create':

                $this->checkPermission(['Owner', 'Office Staff']);

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    $customer_id = intval($_POST['customer_id'] ?? 0);

                    if ($customer_id <= 0) {
                        $_SESSION['order_error'] = "Please select a customer.";
                        header("Location: orders.php?action=create");
                        exit;
                    }

                    $encoded_by = ($_POST['order_source'] == 'Website')
                        ? null
                        : ($_SESSION['user']['user_id'] ?? 1);

                    try {

                        $this->orderModel->createOrder(
                            $customer_id,
                            $_POST['order_source'],
                            $_POST['delivery_address'],
                            $_POST['remarks'],
                            $_POST['product_id'],
                            $_POST['quantity'],
                            $encoded_by
                        );

                        header("Location: orders.php");
                        exit;

                    } catch (Exception $e) {

                        $_SESSION['order_error'] = $e->getMessage();
                    }
                }

                $products = $this->pdo->query("
                    SELECT *
                    FROM products
                    WHERE status='Active'
                ")->fetchAll(PDO::FETCH_ASSOC);

                $customers = $this->pdo->query("
                    SELECT *
                    FROM customers
                    ORDER BY customer_name ASC
                ")->fetchAll(PDO::FETCH_ASSOC);

                include 'app/views/orders/create.php';
                break;


            /*
            |--------------------------------------------------------------------------
            | VIEW ORDER
            |--------------------------------------------------------------------------
            */
            case 'view':

                $order_id = intval($_GET['id']);

                $order = $this->orderModel->getOrderById($order_id);

                if (!$order) {
                    die("Order not found.");
                }

                $items = $this->orderModel->getOrderItems($order_id);

                include 'app/views/orders/view.php';
                break;


            /*
            |--------------------------------------------------------------------------
            | PROCESS ORDER
            |--------------------------------------------------------------------------
            | Allowed transition:
            | Pending -> Processing
            |--------------------------------------------------------------------------
            */
            case 'processing':

                $this->checkPermission(['Owner', 'Office Staff']);

                $id = intval($_GET['id']);

                $order = $this->orderModel->getOrderById($id);

                if (!$order) {
                    die("Order not found.");
                }

                if ($order['status'] !== 'Pending') {

                    $_SESSION['order_error'] =
                        "Only Pending orders can be moved to Processing.";

                    header("Location: orders.php?action=view&id=" . $id);
                    exit;
                }

                $this->orderModel->updateStatus($id, 'Processing');

                $_SESSION['success'] =
                    "Order has been moved to Processing.";

                header("Location: orders.php?action=view&id=" . $id);
                exit;


            /*
            |--------------------------------------------------------------------------
            | COMPLETE ORDER
            |--------------------------------------------------------------------------
            | Allowed transition:
            | Processing -> Completed
            |
            | FIFO inventory deduction happens here.
            |--------------------------------------------------------------------------
            */
            case 'complete':

                $this->checkPermission(['Owner', 'Office Staff']);

                $id = intval($_GET['id']);

                $order = $this->orderModel->getOrderById($id);

                if (!$order) {
                    die("Order not found.");
                }

                /*
                 * Prevent double inventory deduction.
                 */
                if ($order['status'] === 'Completed') {

                    $_SESSION['order_error'] =
                        "This order has already been completed.";

                    header("Location: orders.php?action=view&id=" . $id);
                    exit;
                }

                /*
                 * Prevent skipping the Processing stage.
                 */
                if ($order['status'] !== 'Processing') {

                    $_SESSION['order_error'] =
                        "Only Processing orders can be completed.";

                    header("Location: orders.php?action=view&id=" . $id);
                    exit;
                }

                try {

                    /*
                     * FIFO deduction is performed first.
                     * If there is insufficient stock,
                     * consumeFIFO() throws an exception and
                     * automatically rolls back its transaction.
                     */
                    $this->inventoryController->consumeFIFO($id);


                    $_SESSION['success'] =
                        "Order completed successfully and inventory has been deducted using FIFO.";

                } catch (Exception $e) {

                    $_SESSION['order_error'] =
                        "FIFO Core Error: " . $e->getMessage();
                }

                header("Location: orders.php?action=view&id=" . $id);
                exit;


case 'cancel':

    $this->checkPermission(['Owner', 'Office Staff']);

    $id = isset($_GET['id'])
        ? (int) $_GET['id']
        : 0;

    if ($id <= 0) {
        $_SESSION['order_error'] = "Invalid order ID.";
        header("Location: orders.php");
        exit;
    }

    try {

        /*
         * This method now handles both inventory restoration
         * and the order status update in one transaction.
         */
        $originalStatus =
            $this->inventoryController
                ->cancelOrderAndRestoreInventory($id);

        if ($originalStatus === 'Completed') {

            $_SESSION['success'] =
                "Order cancelled successfully. "
                . "FIFO inventory has been restored.";

        } else {

            $_SESSION['success'] =
                "Order cancelled successfully.";
        }

    } catch (Exception $e) {

        $_SESSION['order_error'] = $e->getMessage();
    }

    header("Location: orders.php?action=view&id=" . $id);
    exit;

            /*
            |--------------------------------------------------------------------------
            | DEFAULT
            |--------------------------------------------------------------------------
            */
            default:

                $orders = $this->orderModel->getAllOrders();

                include 'app/views/orders/index.php';
        }
    }
}