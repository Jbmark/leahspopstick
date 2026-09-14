<?php

require_once __DIR__.'/../models/ExpenseModel.php';

class ExpenseController{

    private $expenseModel;

    public function __construct(){
        $this->expenseModel = new ExpenseModel();
    }

    private function guard(){
        $role = $_SESSION['user']['role_name'] ?? '';
        if(!in_array($role, ['Owner', 'Bookkeeper'])){
            die("Access Denied.");
        }
    }

    public function index(){
        $this->guard();
        $expenses = $this->expenseModel->getAllExpenses();
        include 'app/views/expenses/index.php';
    }

    public function create(){
        $this->guard();

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            try {
                $this->expenseModel->createExpense(
                    $_POST['expense_name'],
                    $_POST['description'],
                    $_POST['amount'],
                    $_POST['expense_date'],
                    $_SESSION['user']['user_id'] ?? 1
                );
                header("Location: expenses.php");
                exit;
            } catch (Exception $e) {
                $_SESSION['expense_error'] = $e->getMessage();
            }
        }

        include 'app/views/expenses/create.php';
    }
}
