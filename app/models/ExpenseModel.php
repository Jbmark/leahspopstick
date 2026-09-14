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
     * 🌟 REALIGNED: Column handles map onto 'created_at', 'title', and 'created_by'
     */
    public function getAllExpenses(){
        return $this->pdo->query("
            SELECT e.expense_id, e.title, e.amount, e.category, e.remarks, e.created_at, u.full_name
            FROM expenses e
            LEFT JOIN users u ON e.created_by = u.user_id
            ORDER BY e.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Commit a fresh expenditure node item safely
     */
    public function createExpense($title, $category, $amount, $remarks, $user_id){
        $stmt = $this->pdo->prepare("
            INSERT INTO expenses (title, category, amount, remarks, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([
            $title,
            $category,
            $amount,
            $remarks,
            $user_id
        ]);
    }
}
