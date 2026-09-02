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
        
        // Call your existing Step 6 Inventory Controller
        $this->inventoryController = new InventoryController();
    }

    /**
     * RBAC Guard: Restrict adjustments using strict system boundary roles
     */
    private function checkPermission($allowedRoles)
    {
        $role = $_SESSION['user']['role_name'] ?? '';
        if (!in_array($role, $allowedRoles)) {
            die("Unauthorized Operation: Your access profile restricts this action.");
        }
    }

    /**
     * Request Router Switch Board
     */
    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
             case 'create':
                $this->checkPermission(['Owner', 'Office Staff']);
                
                // 🌟 FIX: Automatically create a default customer profile if your table is currently empty
                $checkCustomers = $this->pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
                if ($checkCustomers == 0) {
                    $this->pdo->exec("INSERT INTO customers (customer_id, customer_name, contact_number) VALUES (1, 'ABC Ice Drop Factory', '09123456789')");
                }
                
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // System Rule: Website source channel bypasses internal user login logging tracking IDs
                    $encoded_by = ($_POST['order_source'] == 'Website') ? null : ($_SESSION['user']['user_id'] ?? 1);
                    
                    try {
                        $this->orderModel->createOrder(
                            $_POST['customer_id'],
                            $_POST['order_source'],
                            $_POST['delivery_address'],
                            $_POST['remarks'],
                            $_POST['product_id'], // Post Array Form Matrix
                            $_POST['quantity'],   // Post Array Form Matrix
                            $encoded_by
                        );
                        header("Location: orders.php");
                        exit;
                    } catch (Exception $e) {
                        $_SESSION['order_error'] = $e->getMessage();
                    }
                }
                
                // Fetch live data lookups dynamically from normalized tables
                $products = $this->pdo->query("SELECT * FROM products WHERE status='Active'")->fetchAll(PDO::FETCH_ASSOC);
                $customers = $this->pdo->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);
                include 'app/views/orders/create.php';
                break;

            case 'view':
                $order_id = intval($_GET['id']);
                $order = $this->orderModel->getOrderById($order_id);
                $items = $this->orderModel->getOrderItems($order_id);
                include 'app/views/orders/view.php';
                break;

            case 'processing':
                $this->checkPermission(['Owner', 'Office Staff']);
                $this->orderModel->updateStatus(intval($_GET['id']), 'Processing');
                header("Location: orders.php?action=view&id=" . intval($_GET['id']));
                exit;

            case 'complete':
                $this->checkPermission(['Owner','Office Staff']);
                $id = intval($_GET['id']);

                try{
                    // Execute FIFO allocation routines first
                    $this->inventoryController->consumeFIFO($id);

                    // If consumption succeeds, update core parent status flag properties
                    $this->orderModel->updateStatus($id, 'Completed');
                    $_SESSION['success'] = "Order completed successfully and inventory has been deducted using FIFO.";

                }catch(Exception $e){
                    $_SESSION['order_error'] = "FIFO Core Error: " . $e->getMessage();
                }

                header("Location: orders.php?action=view&id=" . $id);
                exit;

            case 'cancel':
                $this->checkPermission(['Owner','Office Staff']);
                $id = intval($_GET['id']);

                try{
                    $order = $this->orderModel->getOrderById($id);

                    // Symmetrical Validation: if previously completed, trigger rollback calculations
                    if($order['status'] == 'Completed'){
                        $this->inventoryController->restoreOrderInventory($id);
                    }

                    // Shift status layout value flag natively
                    $this->orderModel->updateStatus($id, 'Cancelled');
                    $_SESSION['success'] = "Order cancelled successfully and quantities have been restored.";

                }catch(Exception $e){
                    $_SESSION['order_error'] = "Cancellation Rollback Error: " . $e->getMessage();
                }

                header("Location: orders.php?action=view&id=" . $id);
                exit;


            default:
                // Load base tracking records matrix index
                $orders = $this->orderModel->getAllOrders();
                include 'app/views/orders/index.php';
        }
    }
}
