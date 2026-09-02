<?php

class Product{

    private $pdo;

    public function __construct($pdo){
        $this->pdo=$pdo;
    }

    public function getAll(){
        $stmt=$this->pdo->query("
            SELECT *
            FROM products
            ORDER BY product_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $stmt=$this->pdo->prepare("
            INSERT INTO products
            (product_name,description,unit,selling_price,reorder_level)
            VALUES(?,?,?,?,?)
        ");
        return $stmt->execute([
            $data['product_name'],
            $data['description'],
            $data['unit'],
            $data['selling_price'],
            $data['reorder_level']
        ]);
    }

    public function getById($id){
        $stmt=$this->pdo->prepare("
            SELECT *
            FROM products
            WHERE product_id=?
        ");
        $stmt->execute([id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id,$data){
        $stmt=$this->pdo->prepare("
            UPDATE products
            SET
            product_name=?,
            description=?,
            unit=?,
            selling_price=?,
            reorder_level=?
            WHERE product_id=?
        ");
        return $stmt->execute([
            $data['product_name'],
            $data['description'],
            $data['unit'],
            $data['selling_price'],
            $data['reorder_level'],
            $id
        ]);
    }

    public function deactivate($id){
        $stmt=$this->pdo->prepare("
            UPDATE products
            SET status='Inactive'
            WHERE product_id=?
        ");
        return $stmt->execute([$id]);
    }
}
