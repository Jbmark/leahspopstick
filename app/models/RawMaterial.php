<?php

class RawMaterial
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Get all raw materials
    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM raw_materials
            ORDER BY material_name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one raw material
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM raw_materials
            WHERE raw_material_id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add new raw material
    public function create(
        $material_name,
        $description,
        $unit,
        $quantity,
        $reorder_level
    ) {
        $stmt = $this->pdo->prepare("
            INSERT INTO raw_materials
            (
                material_name,
                description,
                unit,
                quantity,
                reorder_level,
                status
            )
            VALUES (?, ?, ?, ?, ?, 'Active')
        ");

        $stmt->execute([
            $material_name,
            $description,
            $unit,
            $quantity,
            $reorder_level
        ]);

        return $this->pdo->lastInsertId();
    }

    // Update raw material information
    public function update(
        $id,
        $material_name,
        $description,
        $unit,
        $reorder_level,
        $status
    ) {
        $stmt = $this->pdo->prepare("
            UPDATE raw_materials
            SET material_name = ?,
                description = ?,
                unit = ?,
                reorder_level = ?,
                status = ?
            WHERE raw_material_id = ?
        ");

        return $stmt->execute([
            $material_name,
            $description,
            $unit,
            $reorder_level,
            $status,
            $id
        ]);
    }

    // Update quantity
    public function updateQuantity($id, $quantity)
    {
        $stmt = $this->pdo->prepare("
            UPDATE raw_materials
            SET quantity = ?
            WHERE raw_material_id = ?
        ");

        return $stmt->execute([
            $quantity,
            $id
        ]);
    }

    // Add stock transaction
    public function addTransaction(
        $raw_material_id,
        $transaction_type,
        $quantity,
        $remarks,
        $created_by
    ) {
        $stmt = $this->pdo->prepare("
            INSERT INTO raw_material_transactions
            (
                raw_material_id,
                transaction_type,
                quantity,
                remarks,
                created_by
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $raw_material_id,
            $transaction_type,
            $quantity,
            $remarks,
            $created_by
        ]);
    }

    // Get transaction history
    public function getTransactions($raw_material_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                t.*,
                u.full_name AS created_by_name
            FROM raw_material_transactions t
            LEFT JOIN users u
                ON t.created_by = u.user_id
            WHERE t.raw_material_id = ?
            ORDER BY t.transaction_date DESC
        ");

        $stmt->execute([$raw_material_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}