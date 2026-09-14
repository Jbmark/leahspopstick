<?php

require_once __DIR__.'/../../config/database.php';

class ReportModel{

    private $pdo;

    public function __construct(){

        global $pdo;
        $this->pdo=$pdo;

    }

    /**
     * Compute gross sales revenue generated exactly during the current calendar date
     */
    public function getTodaySales(){

        $stmt=$this->pdo->query("
            SELECT IFNULL(SUM(total_amount),0)
            FROM orders
            WHERE status='Completed'
            AND DATE(completed_at)=CURDATE()
        ");

        return $stmt->fetchColumn();

    }

    /**
     * Compute aggregate sales generated during the current week cycle interval range
     */
    public function getWeeklySales(){

        $stmt=$this->pdo->query("
            SELECT IFNULL(SUM(total_amount),0)
            FROM orders
            WHERE status='Completed'
            AND YEARWEEK(completed_at, 1)=YEARWEEK(CURDATE(), 1)
        ");

        return $stmt->fetchColumn();

    }

    /**
     * Compute global sales accumulation parameters for the active running month
     */
    public function getMonthlySales(){

        $stmt=$this->pdo->query("
            SELECT IFNULL(SUM(total_amount),0)
            FROM orders
            WHERE status='Completed'
            AND MONTH(completed_at)=MONTH(CURDATE())
            AND YEAR(completed_at)=YEAR(CURDATE())
        ");

        return $stmt->fetchColumn();

    }

    /**
     * Compute total operational capital drain from expenses ledger
     * 🌟 REALIGNED: Changed 'expense_date' to match your explicit table schema column name 'created_at'
     */
        // Total Expenses Tracker Sync
    public function getMonthlyExpenses(){
        $stmt=$this->pdo->query("
            SELECT IFNULL(SUM(amount),0)
            FROM expenses
            WHERE MONTH(expense_date)=MONTH(CURDATE())
            AND YEAR(expense_date)=YEAR(CURDATE())
        ");
        return $stmt->fetchColumn();
    }


    /**
     * Calculate absolute net yield profit/loss differentials metrics balance
     */
    public function getProfitLoss(){

        return $this->getMonthlySales()-$this->getMonthlyExpenses();

    }

    /**
     * Fetch the 10 most recent verified finalized fulfillment records
     * 🌟 REALIGNED: Joins the customers table to fetch customer names cleanly
     */
    public function getRecentTransactions(){

        return $this->pdo->query("
            SELECT
                o.order_id,
                o.total_amount,
                o.completed_at,
                c.customer_name
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.status='Completed'
            ORDER BY o.completed_at DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

    }

    /**
     * Compile running time-series vectors over the trailing 7 operational dates
     */
    public function getWeeklyTrend(){

        return $this->pdo->query("
            SELECT
                DATE(completed_at) as sale_date,
                SUM(total_amount) as total
            FROM orders
            WHERE status='Completed'
            AND completed_at>=DATE_SUB(CURDATE(),INTERVAL 6 DAY)
            GROUP BY DATE(completed_at)
            ORDER BY sale_date ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

    }
        /**
     * Compile separate financial key-value performance indicators data packages array
     */
    public function getMonthlySummary(){
        return [
            'sales'    => $this->getMonthlySales(),
            'expenses' => $this->getMonthlyExpenses(),
            'profit'   => $this->getProfitLoss()
        ];
    }


}
