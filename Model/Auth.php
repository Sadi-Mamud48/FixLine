<?php

class Auth
{
    private PDO $db;

    public function __construct(PDO $databaseConnection)
    {
        $this->db = $databaseConnection;
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function getProviderApprovalStatus(int $userId): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT status FROM service_providers WHERE user_id = :user_id LIMIT 1'
        );
        $stmt->execute([':user_id' => $userId]);
        $status = $stmt->fetchColumn();

        return $status === false ? null : (string) $status;
    }

    public function createCustomer(string $name, string $email, string $password, ?string $phone): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, phone)
             VALUES (:name, :email, :password, 'customer', :phone)"
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':phone' => $phone
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createProvider(
        string $name,
        string $email,
        string $password,
        string $profession,
        ?string $phone,
        ?string $affiliate,
        ?string $experience,
        ?string $bio
    ): int {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, role, phone)
                 VALUES (:name, :email, :password, 'provider', :phone)"
            );
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':phone' => $phone
            ]);

            $userId = (int) $this->db->lastInsertId();
            $stmt = $this->db->prepare(
                "INSERT INTO service_providers
                 (user_id, profession, affiliate, experience, bio, status)
                 VALUES (:user_id, :profession, :affiliate, :experience, :bio, 'pending')"
            );
            $stmt->execute([
                ':user_id' => $userId,
                ':profession' => $profession,
                ':affiliate' => $affiliate,
                ':experience' => $experience,
                ':bio' => $bio
            ]);

            $this->db->commit();
            return $userId;
        } catch (Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
