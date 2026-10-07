<?php

require_once __DIR__.'/../models/ReportModel.php';

class ExportController{

    public function salesCSV(){

        // Only Owner and Bookkeeper can export reports
        $role = $_SESSION['user']['role_name'] ?? '';

        if ($role !== 'Owner' && $role !== 'Bookkeeper') {
            die("Access Denied.");
        }

        $model = new ReportModel();

        // Get all completed sales for the current month
        $data = $model->getMonthlyCompletedOrders();

        // Download as CSV file
        header('Content-Type: text/csv; charset=utf-8');

        header(
            'Content-Disposition: attachment; filename=leahs_popstick_sales_report_' .
            date('Y-m-d') .
            '.csv'
        );

        $output = fopen("php://output", "w");

        // CSV column headers
        fputcsv($output, [
            'Order ID',
            'Customer Name',
            'Completion Date',
            'Total Amount'
        ]);

        // Sales records
        foreach($data as $row){

            fputcsv($output, [
                '#ORD-' . $row['order_id'],
                $row['customer_name'],
                date('Y-m-d h:i A', strtotime($row['completed_at'])),
                number_format($row['total_amount'], 2, '.', '')
            ]);

        }

        fclose($output);
        exit;
    }
}