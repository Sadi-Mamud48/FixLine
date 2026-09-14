<?php

require_once __DIR__ . '/../Config/Database.php';

class Payment
{
    private $pdo;

    public function __construct(?PDO $pdo = null)
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
            "SELECT r.id AS refund_request_id, r.reason, r.status AS refund_status, r.created_at AS requested_at,
                    p.id AS payment_id, p.amount, p.paid_at,
                    u.id AS customer_id, u.name AS customer_name, u.email AS customer_email,
                    s.service_name
             FROM refund_requests r
             JOIN payments p ON p.id = r.payment_id
             JOIN users u ON u.id = r.user_id
             LEFT JOIN bookings b ON b.id = p.booking_id
             LEFT JOIN services s ON s.id = b.service_id
             WHERE r.status = 'requested'
             ORDER BY r.created_at ASC"
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function decideRefundRequest(int $refundRequestId, string $decision): bool
    {
        if (!in_array($decision, ['approved', 'rejected'], true)) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $request = $this->pdo->prepare(
                "SELECT payment_id FROM refund_requests
                 WHERE id = :request_id AND status = 'requested'
                 FOR UPDATE"
            );
            $request->execute(['request_id' => $refundRequestId]);
            $paymentId = $request->fetchColumn();

            if ($paymentId === false) {
                $this->pdo->rollBack();
                return false;
            }

            $updateRequest = $this->pdo->prepare(
                "UPDATE refund_requests SET status = :decision WHERE id = :request_id"
            );
            $updateRequest->execute([
                'decision' => $decision,
                'request_id' => $refundRequestId,
            ]);

            if ($decision === 'approved') {
                $updatePayment = $this->pdo->prepare(
                    "UPDATE payments SET status = 'Refunded' WHERE id = :payment_id AND status = 'paid'"
                );
                $updatePayment->execute(['payment_id' => $paymentId]);
                if ($updatePayment->rowCount() !== 1) {
                    $this->pdo->rollBack();
                    return false;
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
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
            "SELECT r.id AS refund_request_id, r.status, r.reason, r.created_at AS decided_at,
                    p.id AS payment_id, p.amount,
                    u.id AS customer_id, u.name AS customer_name, u.email AS customer_email
             FROM refund_requests r
             JOIN payments p ON p.id = r.payment_id
             JOIN users u ON u.id = r.user_id
             WHERE r.status IN ('approved', 'rejected')
             ORDER BY r.created_at DESC"
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
