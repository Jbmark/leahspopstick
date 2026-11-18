
<?php

require_once __DIR__.'/../../config/database.php';

class ExpenseModel{

    private $pdo;

    public function __construct(){
        global $pdo;
        $this->pdo=$pdo;
    }

    public function getAllExpenses(){
        return $this->pdo->query("
            SELECT e.expense_id, e.expense_name, e.category, e.amount,
                   e.description, e.expense_date, u.full_name
            FROM expenses e
            LEFT JOIN users u ON e.recorded_by = u.user_id
            ORDER BY e.expense_date DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createExpense($name, $category, $description, $amount, $date, $user_id){
        $stmt = $this->pdo->prepare("
            INSERT INTO expenses
            (expense_name, category, description, amount, expense_date, recorded_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $name,
            $category,
            $description,
            $amount,
            $date,
            $user_id
        ]);
    }
}
