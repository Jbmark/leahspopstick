<?php

class CustomerModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Get all customers
    public function getAllCustomers()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM customers
            ORDER BY customer_name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one customer by ID
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM customers
            WHERE customer_id = ?
        ");

        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find customer using contact number
    public function findByContact($contact)
    {
        $contact = trim($contact);

        if ($contact === '') {
            return false;
        }

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM customers
            WHERE contact_number = ?
            LIMIT 1
        ");

        $stmt->execute([
            $contact
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create customer
    public function create($name, $contact, $email, $address)
    {
        $name = trim($name);
        $contact = trim($contact);
        $email = trim($email);
        $address = trim($address);

        if ($name === '') {
            throw new Exception("Customer name is required.");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO customers
            (
                customer_name,
                contact_number,
                email,
                address
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $contact !== '' ? $contact : null,
            $email !== '' ? $email : null,
            $address !== '' ? $address : null
        ]);

        return $this->pdo->lastInsertId();
    }

    // Update customer
    public function update(
        $id,
        $name,
        $contact,
        $email,
        $address
    ) {
        $name = trim($name);
        $contact = trim($contact);
        $email = trim($email);
        $address = trim($address);

        if ($name === '') {
            throw new Exception("Customer name is required.");
        }

        $stmt = $this->pdo->prepare("
            UPDATE customers
            SET
                customer_name = ?,
                contact_number = ?,
                email = ?,
                address = ?
            WHERE customer_id = ?
        ");

        return $stmt->execute([
            $name,
            $contact !== '' ? $contact : null,
            $email !== '' ? $email : null,
            $address !== '' ? $address : null,
            $id
        ]);
    }
}
