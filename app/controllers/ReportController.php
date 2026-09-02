<?php

require_once __DIR__.'/../models/ReportModel.php';
require_once __DIR__.'/../models/OrderModel.php'; // Ensure model is imported here

class ReportController{

    private $reportModel;
    private $orderModel;

    public function __construct(){
        $this->reportModel = new ReportModel();
        $this->orderModel = new OrderModel(); // Instantiate structural handling models
    }

    public function dashboard(){
        $role = $_SESSION['user']['role_name'] ?? '';
        if ($role !== 'Owner' && $role !== 'Bookkeeper') {
            die("Access Denied.");
        }

        $todaySales   = $this->reportModel->getTodaySales();
        $weeklySales  = $this->reportModel->getWeeklySales();
        $monthlySales = $this->reportModel->getMonthlySales();
        $expenses     = $this->reportModel->getMonthlyExpenses();
        $profit       = $this->reportModel->getProfitLoss();
        $recent       = $this->reportModel->getRecentTransactions();
        $trend        = $this->reportModel->getWeeklyTrend();
        
        // 🌟 STEP 10D ATTACHED: Fetch bundled metrics statistics values natively
        $summary      = $this->reportModel->getMonthlySummary();

        include 'app/views/reports/dashboard.php';
    }


    /**
     * Compile separate printable receipt layouts for distinct orders
     */
    public function invoice($id){
        $id = intval($id);
        $order = $this->orderModel->getOrderById($id);
        
        if (!$order) {
            die("Error Matrix: Specified order parameters record missing.");
        }

        $items = $this->orderModel->getOrderItems($id);
        include 'app/views/reports/invoice.php';
    }

    /**
     * Compile separate monthly total document summary grids
     */
    public function printSales(){
        $monthlySales = $this->reportModel->getMonthlySales();
        $recent = $this->reportModel->getRecentTransactions();
        
        include 'app/views/reports/print_sales.php';
    }
}
