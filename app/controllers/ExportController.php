<?php

require_once __DIR__.'/../models/ReportModel.php';

class ExportController{

    /**
     * Compile and stream a downloadable Excel-ready spreadsheet ledger file
     */
    public function salesCSV(){

        // Security Checkpoint: Restrict export logs strictly to allowed accounting roles
        $role = $_SESSION['user']['role_name'] ?? '';
        if ($role !== 'Owner' && $role !== 'Bookkeeper') {
            die("Access Denied: Your assigned account profile restricts document export downloads.");
        }

        $model = new ReportModel();
        $data = $model->getRecentTransactions();

        // Establish core headers to trick the system browser into forcing binary data downloads
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=leahs_popstick_sales_report_'.date('Y-m-d').'.csv');

        // Open the live browser response output stream channel memory buffer
        $output = fopen("php://output", "w");

        // Insert Excel Column Header Labels Matrix
        fputcsv($output, ['Order ID', 'Customer Entity Profile Name', 'Fulfillment Date Completed', 'Total Amount Realized (₱)']);

        // Loop, clean fields array cells, and append file text rows
        foreach($data as $row){
            fputcsv($output, [
                '#ORD-' . $row['order_id'],
                $row['customer_name'], // Expanded variable field maps natively due to step 9A model enhancements
                date('Y-m-d h:i A', strtotime($row['completed_at'])),
                number_format($row['total_amount'], 2, '.', '') // Keep raw numeric values clean for Excel math formulas execution
            ]);
        }

        fclose($output);
        exit;
    }
}
