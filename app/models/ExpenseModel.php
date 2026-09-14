<?php

require_once __DIR__.'/../../config/database.php';

class ExpenseModel{

    private $pdo;

    public function __construct(){
        global $pdo;
        $this->pdo=$pdo;
    }

    /**
     * Fetch all logged expenditures joined with the target accounts encoder name
     * 🌟 FIXED: Maps precisely to your active columns: expense_name, expense_date, and recorded_by
     */
    public function getAllExpenses(){
        return $this->pdo->query("
            SELECT e.expense_id, e.expense_name, e.amount, e.description, e.expense_date, u.full_name
            FROM expenses e
            LEFT JOIN users u ON e.recorded_by = u.user_id
            ORDER BY e.expense_date DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Commit a fresh expenditure node item safely
     */
    public function createExpense($name, $description, $amount, $date, $user_id){
        $stmt = $this->pdo->prepare("
            INSERT INTO expenses (expense_name, description, amount, expense_date, recorded_by)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $name,
            $description,
            $amount,
            $date,
            $user_id
        ]);
    }
}
