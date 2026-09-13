<?php

require_once __DIR__ . '/../config/database.php';

class Payment
{
    private $pdo;

    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?: createDatabaseConnection();
    }

    public function getAllTransactions($invoice = '', $startDate = '', $endDate = '')
    {
        $conditions = [];
        $parameters = [];

        if ($invoice !== '') {
            $conditions[] = 'invoice_id LIKE :invoice';
            $parameters['invoice'] = '%' . $invoice . '%';
        }

        if ($startDate !== '') {
            $conditions[] = 'payment_date >= :start_date';
            $parameters['start_date'] = $startDate . ' 00:00:00';
        }

        if ($endDate !== '') {
            $conditions[] = 'payment_date <= :end_date';
            $parameters['end_date'] = $endDate . ' 23:59:59';
        }

        $query = 'SELECT invoice_id, payment_date, customer_name, payment_method, amount, status FROM payments';

        if ($conditions) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY payment_date DESC';

        $statement = $this->pdo->prepare($query);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function createPayment($invoiceId, $paymentDate, $customerId, $customerName, $customerEmail, $paymentMethod, $amount)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO payments
                (invoice_id, customer_id, customer_name, customer_email, payment_method, amount, payment_date, status)
             VALUES
                (:invoice_id, :customer_id, :customer_name, :customer_email, :payment_method, :amount, :payment_date, 'Approved')"
        );

        $statement->execute([
            'invoice_id' => $invoiceId,
            'customer_id' => $customerId,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'payment_date' => $paymentDate . ' 00:00:00',
        ]);
    }

    public function getPendingPayouts()
    {
        $statement = $this->pdo->prepare(
            "SELECT invoice_id, payment_date, customer_name, payment_method, amount, status
             FROM payments
             WHERE status = 'Pending'
             ORDER BY payment_date DESC"
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getRefundRequests()
    {
        $statement = $this->pdo->prepare(
            "SELECT invoice_id, payment_date, customer_name, payment_method, amount, status
             FROM payments
             WHERE status = 'Refunded'
             ORDER BY payment_date DESC"
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getRefundablePayments()
    {
        $statement = $this->pdo->prepare(
            "SELECT payment_id, invoice_id, customer_id, customer_name, customer_email, amount, payment_method, payment_date
             FROM payments
             WHERE status = 'Approved'
             ORDER BY payment_date DESC"
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function markAsRefunded($invoiceId)
    {
        $statement = $this->pdo->prepare(
            "UPDATE payments SET status = 'Refunded'
             WHERE invoice_id = :invoice_id AND status = 'Approved'"
        );
        $statement->execute(['invoice_id' => $invoiceId]);

        return $statement->rowCount() > 0;
    }

    public function rejectRefund($invoiceId)
    {
        $statement = $this->pdo->prepare(
            "UPDATE payments SET status = 'Rejected'
             WHERE invoice_id = :invoice_id AND status = 'Approved'"
        );
        $statement->execute(['invoice_id' => $invoiceId]);

        return $statement->rowCount() > 0;
    }

    public function getRefundHistory()
    {
        $statement = $this->pdo->prepare(
            "SELECT invoice_id, customer_id, customer_name, customer_email, amount, payment_date, status
             FROM payments
             WHERE status IN ('Refunded', 'Rejected')
             ORDER BY payment_date DESC"
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getProviderPayouts($provider = '', $startDate = '', $endDate = '')
    {
        $conditions = [];
        $parameters = [];

        if ($provider !== '') {
            $conditions[] = 'provider_name LIKE :provider';
            $parameters['provider'] = '%' . $provider . '%';
        }

        if ($startDate !== '') {
            $conditions[] = 'payout_date >= :start_date';
            $parameters['start_date'] = $startDate . ' 00:00:00';
        }

        if ($endDate !== '') {
            $conditions[] = 'payout_date <= :end_date';
            $parameters['end_date'] = $endDate . ' 23:59:59';
        }

        $query = 'SELECT provider_name, job_id, invoice_id, amount, payout_method, payout_date, status FROM payouts';

        if ($conditions) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY payout_date DESC';

        $statement = $this->pdo->prepare($query);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }
}
