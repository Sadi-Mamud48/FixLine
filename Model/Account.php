<?php

require_once __DIR__ . '/../config/database.php';

class Account
{
    private $pdo;

    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?: createDatabaseConnection();
    }

    public function getAll($search = '', $type = '')
    {
        $conditions = [];
        $parameters = [];

        if ($search !== '') {
            $conditions[] = '(account_id LIKE :search OR name LIKE :search OR email LIKE :search)';
            $parameters['search'] = '%' . $search . '%';
        }

        if ($type !== '') {
            $conditions[] = 'account_type = :account_type';
            $parameters['account_type'] = $type;
        }

        $query = 'SELECT account_id, account_type, name, email, status, last_payment, last_payout, last_login FROM accounts';
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
        if (!in_array($status, ['Active', 'Pending', 'Suspended'], true)) {
            return false;
        }

        $statement = $this->pdo->prepare(
            'UPDATE accounts SET status = :status WHERE account_id = :account_id'
        );
        $statement->execute([
            'status' => $status,
            'account_id' => $accountId,
        ]);

        return $statement->rowCount() > 0;
    }
}
