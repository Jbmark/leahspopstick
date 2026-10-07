<?php

require_once 'config/database.php';
require_once 'app/models/CustomerModel.php';

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        global $pdo;

        $this->customerModel = new CustomerModel($pdo);
    }

    // Check if user is allowed
    private function checkPermission()
    {
        $role = $_SESSION['user']['role_name'] ?? '';

        if (!in_array($role, ['Owner', 'Office Staff'])) {
            die("Access Denied.");
        }
    }

    // Customer list
    public function index()
    {
        $this->checkPermission();

        $customers = $this->customerModel->getAllCustomers();

        include 'app/views/customers/index.php';
    }

    // Add customer
    public function create()
    {
        $this->checkPermission();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['customer_name'] ?? '');
            $contact = trim($_POST['contact_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($name === '') {

                $_SESSION['error'] =
                    "Customer name is required.";

                header(
                    "Location: customers.php?action=create"
                );

                exit;
            }

            try {

                $this->customerModel->create(
                    $name,
                    $contact,
                    $email,
                    $address
                );

                $_SESSION['success'] =
                    "Customer added successfully.";

                header("Location: customers.php");
                exit;

            } catch (Exception $e) {

                $_SESSION['error'] =
                    $e->getMessage();

                header(
                    "Location: customers.php?action=create"
                );

                exit;
            }
        }

        include 'app/views/customers/create.php';
    }

    // Edit customer
    public function edit($id)
    {
        $this->checkPermission();

        $id = intval($id);

        $customer = $this->customerModel->getById($id);

        if (!$customer) {

            die("Customer not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['customer_name'] ?? '');
            $contact = trim($_POST['contact_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($name === '') {

                $_SESSION['error'] =
                    "Customer name is required.";

                header(
                    "Location: customers.php?action=edit&id=" . $id
                );

                exit;
            }

            try {

                $this->customerModel->update(
                    $id,
                    $name,
                    $contact,
                    $email,
                    $address
                );

                $_SESSION['success'] =
                    "Customer updated successfully.";

                header("Location: customers.php");
                exit;

            } catch (Exception $e) {

                $_SESSION['error'] =
                    $e->getMessage();

                header(
                    "Location: customers.php?action=edit&id=" . $id
                );

                exit;
            }
        }

        include 'app/views/customers/edit.php';
    }
}