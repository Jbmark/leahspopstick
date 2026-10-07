<?php

require_once __DIR__.'/../models/ReportModel.php';
require_once __DIR__.'/../models/OrderModel.php';

class ReportController
{
    private $reportModel;
    private $orderModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->orderModel = new OrderModel();
    }

    public function dashboard()
    {
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
        $summary      = $this->reportModel->getMonthlySummary();
        $inventory    = $this->reportModel->getInventorySummary();

        // Date range filter
        $fromDate = $_GET['from_date'] ?? '';
        $toDate   = $_GET['to_date'] ?? '';

        $dateRange = null;

        if (!empty($fromDate) && !empty($toDate)) {

            if ($fromDate <= $toDate) {

                $dateRange = $this->reportModel->getDateRangeSummary(
                    $fromDate,
                    $toDate
                );
            }
        }

        include 'app/views/reports/dashboard.php';
    }

    public function invoice($id)
    {
        $id = intval($id);

        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            die("Error Matrix: Specified order parameters record missing.");
        }

        $items = $this->orderModel->getOrderItems($id);

        include 'app/views/reports/invoice.php';
    }

    public function printSales()
    {
        $monthlySales = $this->reportModel->getMonthlySales();

        // Get all completed orders for the current month
        $recent = $this->reportModel->getMonthlyCompletedOrders();

        include 'app/views/reports/print_sales.php';
    }
}