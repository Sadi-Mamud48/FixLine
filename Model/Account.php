<?php

require_once __DIR__ . '/../Config/Database.php';

class Account
{
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?: createDatabaseConnection();
    }

    public function getAll($search = '', $type = '')
    {
        $conditions = [];
        $parameters = [];

        if ($search !== '') {
            $conditions[] = '(id LIKE :search OR name LIKE :search OR email LIKE :search)';
            $parameters['search'] = '%' . $search . '%';
        }

        if ($type !== '') {
            $conditions[] = 'role = :account_type';
            $parameters['account_type'] = $type;
        }

        $query = "SELECT id AS account_id, role AS account_type, name, email, status,
               NULL AS last_payment, NULL AS last_payout, NULL AS last_login
               FROM users";
        if ($conditions) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $query .= ' ORDER BY account_type, name';

        $statement = $this->pdo->prepare($query);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function updateStatus($accountId, $status)
    {
        if (!filter_var($accountId, FILTER_VALIDATE_INT) || !in_array($status, ['Active', 'Blocked'], true)) {
            return false;
        }

        $statement = $this->pdo->prepare(
            'UPDATE users SET status = :status WHERE id = :account_id'
        );
        return $statement->execute([
            'status' => $status,
            'account_id' => $accountId,
        ]);
    }
}
