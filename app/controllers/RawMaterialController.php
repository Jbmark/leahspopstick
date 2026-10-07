<?php

require_once __DIR__ . '/../models/RawMaterial.php';
require_once __DIR__ . '/../../config/database.php';

class RawMaterialController
{
    private $rawMaterialModel;
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
        $this->rawMaterialModel = new RawMaterial($pdo);
    }

    // Allow Owner and Office Staff only
    private function checkPermission()
    {
        $role = $_SESSION['user']['role_name'] ?? '';

        if (!in_array($role, ['Owner', 'Office Staff'])) {
            die("Access Denied.");
        }
    }

    // Get logged-in user ID
    private function getUserId()
    {
        return $_SESSION['user']['user_id'] ?? null;
    }

    public function handleRequest()
    {
        $this->checkPermission();

        $action = $_GET['action'] ?? 'index';

        switch ($action) {

            // =========================
            // LIST RAW MATERIALS
            // =========================
            case 'index':

                $rawMaterials = $this->rawMaterialModel->getAll();

                include 'app/views/raw_materials/index.php';

                break;


            // =========================
            // ADD RAW MATERIAL
            // =========================
            case 'create':

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $material_name = trim($_POST['material_name'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $unit = trim($_POST['unit'] ?? '');
                    $quantity = floatval($_POST['quantity'] ?? 0);
                    $reorder_level = floatval($_POST['reorder_level'] ?? 0);

                    if ($material_name === '' || $unit === '') {

                        $_SESSION['raw_material_error'] =
                            "Material name and unit are required.";

                        header("Location: raw_materials.php?action=create");
                        exit;
                    }

                    if ($quantity < 0 || $reorder_level < 0) {

                        $_SESSION['raw_material_error'] =
                            "Quantity and reorder level cannot be negative.";

                        header("Location: raw_materials.php?action=create");
                        exit;
                    }

                    $id = $this->rawMaterialModel->create(
                        $material_name,
                        $description,
                        $unit,
                        $quantity,
                        $reorder_level
                    );

                    // If starting quantity was entered,
                    // record it as an initial Stock In.
                    if ($quantity > 0) {

                        $this->rawMaterialModel->addTransaction(
                            $id,
                            'Stock In',
                            $quantity,
                            'Initial stock',
                            $this->getUserId()
                        );
                    }

                    $_SESSION['raw_material_success'] =
                        "Raw material added successfully.";

                    header("Location: raw_materials.php");
                    exit;
                }

                include 'app/views/raw_materials/create.php';

                break;


            // =========================
            // EDIT RAW MATERIAL
            // =========================
            case 'edit':

                $id = intval($_GET['id'] ?? 0);

                $rawMaterial = $this->rawMaterialModel->getById($id);

                if (!$rawMaterial) {
                    die("Raw material not found.");
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $material_name = trim($_POST['material_name'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $unit = trim($_POST['unit'] ?? '');
                    $reorder_level = floatval(
                        $_POST['reorder_level'] ?? 0
                    );
                    $status = $_POST['status'] ?? 'Active';

                    if ($material_name === '' || $unit === '') {

                        $_SESSION['raw_material_error'] =
                            "Material name and unit are required.";

                        header(
                            "Location: raw_materials.php?action=edit&id=" . $id
                        );
                        exit;
                    }

                    if ($reorder_level < 0) {

                        $_SESSION['raw_material_error'] =
                            "Reorder level cannot be negative.";

                        header(
                            "Location: raw_materials.php?action=edit&id=" . $id
                        );
                        exit;
                    }

                    if (!in_array($status, ['Active', 'Inactive'])) {
                        $status = 'Active';
                    }

                    $this->rawMaterialModel->update(
                        $id,
                        $material_name,
                        $description,
                        $unit,
                        $reorder_level,
                        $status
                    );

                    $_SESSION['raw_material_success'] =
                        "Raw material updated successfully.";

                    header("Location: raw_materials.php");
                    exit;
                }

                include 'app/views/raw_materials/edit.php';

                break;


            // =========================
            // STOCK IN
            // =========================
            case 'stock_in':

                $id = intval($_GET['id'] ?? 0);

                $rawMaterial = $this->rawMaterialModel->getById($id);

                if (!$rawMaterial) {
                    die("Raw material not found.");
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $quantity = floatval($_POST['quantity'] ?? 0);
                    $remarks = trim($_POST['remarks'] ?? '');

                    if ($quantity <= 0) {

                        $_SESSION['raw_material_error'] =
                            "Stock In quantity must be greater than zero.";

                        header(
                            "Location: raw_materials.php?action=stock_in&id=" . $id
                        );
                        exit;
                    }

                    $newQuantity =
                        $rawMaterial['quantity'] + $quantity;

                    $this->pdo->beginTransaction();

                    try {

                        $this->rawMaterialModel->updateQuantity(
                            $id,
                            $newQuantity
                        );

                        $this->rawMaterialModel->addTransaction(
                            $id,
                            'Stock In',
                            $quantity,
                            $remarks,
                            $this->getUserId()
                        );

                        $this->pdo->commit();

                        $_SESSION['raw_material_success'] =
                            "Stock In recorded successfully.";

                        header("Location: raw_materials.php");
                        exit;

                    } catch (Exception $e) {

                        $this->pdo->rollBack();

                        $_SESSION['raw_material_error'] =
                            "Stock In failed: " . $e->getMessage();

                        header(
                            "Location: raw_materials.php?action=stock_in&id=" . $id
                        );
                        exit;
                    }
                }

                include 'app/views/raw_materials/stock_in.php';

                break;


            // =========================
            // STOCK OUT
            // =========================
            case 'stock_out':

                $id = intval($_GET['id'] ?? 0);

                $rawMaterial = $this->rawMaterialModel->getById($id);

                if (!$rawMaterial) {
                    die("Raw material not found.");
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $quantity = floatval($_POST['quantity'] ?? 0);
                    $remarks = trim($_POST['remarks'] ?? '');

                    if ($quantity <= 0) {

                        $_SESSION['raw_material_error'] =
                            "Stock Out quantity must be greater than zero.";

                        header(
                            "Location: raw_materials.php?action=stock_out&id=" . $id
                        );
                        exit;
                    }

                    if ($quantity > $rawMaterial['quantity']) {

                        $_SESSION['raw_material_error'] =
                            "Stock Out quantity cannot exceed available stock.";

                        header(
                            "Location: raw_materials.php?action=stock_out&id=" . $id
                        );
                        exit;
                    }

                    $newQuantity =
                        $rawMaterial['quantity'] - $quantity;

                    $this->pdo->beginTransaction();

                    try {

                        $this->rawMaterialModel->updateQuantity(
                            $id,
                            $newQuantity
                        );

                        $this->rawMaterialModel->addTransaction(
                            $id,
                            'Stock Out',
                            $quantity,
                            $remarks,
                            $this->getUserId()
                        );

                        $this->pdo->commit();

                        $_SESSION['raw_material_success'] =
                            "Stock Out recorded successfully.";

                        header("Location: raw_materials.php");
                        exit;

                    } catch (Exception $e) {

                        $this->pdo->rollBack();

                        $_SESSION['raw_material_error'] =
                            "Stock Out failed: " . $e->getMessage();

                        header(
                            "Location: raw_materials.php?action=stock_out&id=" . $id
                        );
                        exit;
                    }
                }

                include 'app/views/raw_materials/stock_out.php';

                break;


            // =========================
            // ADJUSTMENT
            // =========================
            case 'adjustment':

                $id = intval($_GET['id'] ?? 0);

                $rawMaterial = $this->rawMaterialModel->getById($id);

                if (!$rawMaterial) {
                    die("Raw material not found.");
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $quantity = floatval($_POST['quantity'] ?? 0);
                    $remarks = trim($_POST['remarks'] ?? '');

                    if ($quantity < 0) {

                        $_SESSION['raw_material_error'] =
                            "Adjusted quantity cannot be negative.";

                        header(
                            "Location: raw_materials.php?action=adjustment&id=" . $id
                        );
                        exit;
                    }

                    $this->pdo->beginTransaction();

                    try {

                        $this->rawMaterialModel->updateQuantity(
                            $id,
                            $quantity
                        );

                        $this->rawMaterialModel->addTransaction(
                            $id,
                            'Adjustment',
                            $quantity,
                            $remarks,
                            $this->getUserId()
                        );

                        $this->pdo->commit();

                        $_SESSION['raw_material_success'] =
                            "Inventory adjustment recorded successfully.";

                        header("Location: raw_materials.php");
                        exit;

                    } catch (Exception $e) {

                        $this->pdo->rollBack();

                        $_SESSION['raw_material_error'] =
                            "Adjustment failed: " . $e->getMessage();

                        header(
                            "Location: raw_materials.php?action=adjustment&id=" . $id
                        );
                        exit;
                    }
                }

                include 'app/views/raw_materials/adjustment.php';

                break;


            // =========================
            // TRANSACTION HISTORY
            // =========================
            case 'history':

                $id = intval($_GET['id'] ?? 0);

                $rawMaterial = $this->rawMaterialModel->getById($id);

                if (!$rawMaterial) {
                    die("Raw material not found.");
                }

                $transactions =
                    $this->rawMaterialModel->getTransactions($id);

                include 'app/views/raw_materials/history.php';

                break;


            default:

                header("Location: raw_materials.php");
                exit;
        }
    }
}