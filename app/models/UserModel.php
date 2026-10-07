<?php

class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT
                u.user_id,
                u.full_name,
                u.username,
                u.email,
                u.status,
                u.created_at,
                r.role_name
            FROM users u
            JOIN roles r
                ON u.role_id = r.role_id
            ORDER BY u.full_name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.*,
                r.role_name
            FROM users u
            JOIN roles r
                ON u.role_id = r.role_id
            WHERE u.user_id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoles()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM roles
            ORDER BY role_id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        $full_name,
        $username,
        $password,
        $email,
        $role_id
    ) {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->pdo->prepare("
            INSERT INTO users
            (
                full_name,
                username,
                password,
                email,
                role_id,
                status
            )
            VALUES (?, ?, ?, ?, ?, 'Active')
        ");

        return $stmt->execute([
            $full_name,
            $username,
            $hashedPassword,
            $email,
            $role_id
        ]);
    }

    public function update(
        $id,
        $full_name,
        $username,
        $email,
        $role_id
    ) {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET
                full_name = ?,
                username = ?,
                email = ?,
                role_id = ?
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $full_name,
            $username,
            $email,
            $role_id,
            $id
        ]);
    }

    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->pdo->prepare("
            UPDATE users
            SET password = ?
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $hashedPassword,
            $id
        ]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET status = ?
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $status,
            $id
        ]);
    }

    public function usernameExists($username, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("
                SELECT user_id
                FROM users
                WHERE username = ?
                AND user_id != ?
                LIMIT 1
            ");

            $stmt->execute([
                $username,
                $excludeId
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT user_id
                FROM users
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}