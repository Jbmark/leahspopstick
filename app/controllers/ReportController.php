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

    // Clear resets the report to the default Monthly view.
    $period = isset($_GET['clear'])
        ? 'monthly'
        : ($_GET['period'] ?? 'monthly');

    $allowedPeriods = [
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'custom'
    ];

    if (!in_array($period, $allowedPeriods, true)) {
        $period = 'monthly';
    }

    $today = date('Y-m-d');

    // Default dates
    $fromDate = '';
    $toDate = '';
    $dateRange = null;
    $dateError = '';

    // Set dates automatically based on the selected period.
    if ($period === 'daily') {
        $fromDate = $today;
        $toDate = $today;

    } elseif ($period === 'weekly') {
        // Monday to Sunday
        $fromDate = date('Y-m-d', strtotime('monday this week'));
        $toDate = date('Y-m-d', strtotime('sunday this week'));

    } elseif ($period === 'monthly') {
        $fromDate = date('Y-m-01');
        $toDate = date('Y-m-t');

    } elseif ($period === 'yearly') {
        $fromDate = date('Y-01-01');
        $toDate = date('Y-12-31');

    } elseif ($period === 'custom') {

        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';

        // Generate the custom report only when both dates are supplied.
        if ($fromDate !== '' && $toDate !== '') {

            $validDate = function ($value) {
                if (!is_string($value)) {
                    return false;
                }

                $date = DateTime::createFromFormat('!Y-m-d', $value);

                return $date !== false
                    && $date->format('Y-m-d') === $value;
            };

            if (
                !$validDate($fromDate) ||
                !$validDate($toDate) ||
                $fromDate > $toDate
            ) {
                $dateError = 'Please select valid dates. The start date must not be after the end date.';
                $fromDate = '';
                $toDate = '';
            }
        } else {
            // Keep the custom date fields empty until the user selects dates.
            $fromDate = '';
            $toDate = '';
        }
    }

    // Get the financial summary for the selected period.
    if (
        $period !== 'custom' ||
        ($fromDate !== '' && $toDate !== '' && $dateError === '')
    ) {
        $dateRange = $this->reportModel->getDateRangeSummary(
            $fromDate,
            $toDate
        );
    }

    // Existing dashboard information — keep these unchanged.
    $todaySales = $this->reportModel->getTodaySales();
    $weeklySales = $this->reportModel->getWeeklySales();
    $monthlySales = $this->reportModel->getMonthlySales();
    $expenses = $this->reportModel->getMonthlyExpenses();
    $profit = $this->reportModel->getProfitLoss();
    $recent = $this->reportModel->getRecentTransactions();
    $trend = $this->reportModel->getTrendForPeriod(
    $fromDate,
    $toDate,
    $period
);
    $summary = $this->reportModel->getMonthlySummary();
    $inventory = $this->reportModel->getInventorySummary();

    $periodLabel = ucfirst($period);

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