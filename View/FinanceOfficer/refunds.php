<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Payment.php';

$payments = [];
$refundHistory = [];
$message = '';
$messageType = '';
$selectedInvoice = trim($_GET['invoice'] ?? $_POST['invoice_id'] ?? '');

try {
    $paymentModel = new Payment();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['approve_refund']) || isset($_POST['reject_refund']))) {
        if ($selectedInvoice === '') {
            throw new InvalidArgumentException('Select a payment before submitting a refund.');
        }

        $updated = isset($_POST['approve_refund'])
            ? $paymentModel->markAsRefunded($selectedInvoice)
            : $paymentModel->rejectRefund($selectedInvoice);

        if ($updated && isset($_POST['approve_refund'])) {
            $message = 'Full refund approved successfully.';
            $messageType = 'success';
        } elseif ($updated) {
            $message = 'Refund request rejected.';
            $messageType = 'success';
        } else {
            $message = 'This payment is no longer available for a refund decision.';
            $messageType = 'error';
        }
    }

    $payments = $paymentModel->getRefundablePayments();
    $refundHistory = $paymentModel->getRefundHistory();
} catch (Throwable $exception) {
    $message = $exception instanceof InvalidArgumentException
        ? $exception->getMessage()
        : 'Customer refund records are temporarily unavailable. Please check the database connection.';
    $messageType = 'error';
}

$selectedPayment = null;
foreach ($payments as $payment) {
    if ($payment['invoice_id'] === $selectedInvoice) {
        $selectedPayment = $payment;
        break;
    }
}

if (!$selectedPayment && $payments) {
    $selectedPayment = $payments[0];
    $selectedInvoice = $selectedPayment['invoice_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refunds - FixLine</title>
    <style>
        :root { --purple: #5b3dcc; --deep-purple: #2c167e; --ink: #25243a; --muted: #77758a; --line: #e9e7f1; --orange: #f28b1a; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #dff3ff; color: var(--ink); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .page-shell { width: min(980px, calc(100% - 40px)); margin: 24px auto; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px 26px; color: #fff; background: var(--purple); border-radius: 18px 18px 0 0; }
        .brand { display: flex; align-items: center; gap: 12px; font-size: 19px; font-weight: 600; }
        .brand img { width: 38px; height: 38px; object-fit: contain; }
        .back-link { color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,.55); padding: 9px 14px; border-radius: 8px; font-size: 14px; }
        main { padding: 34px 42px; background: #fff; border-radius: 0 0 18px 18px; box-shadow: 0 12px 30px rgba(44, 22, 126, .12); }
        h1 { margin: 0; font-size: 28px; }
        .subtitle { margin: 7px 0 24px; color: var(--muted); }
        .notice { padding: 13px 16px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .notice.success { color: #176a3a; background: #e5f8ed; }
        .notice.error { color: #8b3f32; background: #fff0ed; }
        .payment-picker { margin-bottom: 26px; }
        label { display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px; }
        select, input, textarea { width: 100%; padding: 12px 14px; border: 1px solid #dedde8; border-radius: 8px; color: var(--ink); background: #fff; font: inherit; }
        textarea { min-height: 88px; resize: vertical; }
        .transaction { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 34px; padding: 18px 14px; margin-bottom: 28px; background: #e3f4ff; }
        .transaction-row { display: flex; justify-content: space-between; gap: 14px; color: var(--muted); font-size: 14px; }
        .transaction-row strong { color: var(--ink); text-align: right; }
        .amount-box { padding: 18px; margin-bottom: 22px; border: 1px solid var(--line); border-radius: 8px; background: #fbfbfd; }
        .amount-box input { font-size: 18px; font-weight: 600; background: #e3f4ff; }
        .full-refund-note { margin: 8px 0 0; color: #16a6ad; font-size: 13px; }
        .decision-note { margin-top: 20px; color: var(--muted); font-size: 13px; }
        .actions { display: flex; justify-content: flex-end; gap: 14px; margin-top: 28px; }
        button { font: inherit; cursor: pointer; }
        .cancel { padding: 12px 36px; border: 0; border-radius: 7px; color: var(--deep-purple); background: #eef0f3; text-decoration: none; }
        .reject { padding: 12px 28px; border: 0; border-radius: 7px; color: #8b3f32; background: #fff0ed; }
        .refund { padding: 12px 28px; border: 0; border-radius: 7px; color: #fff; background: var(--orange); }
        .empty-state { padding: 28px 0; color: var(--muted); text-align: center; }
        .history { margin-top: 38px; padding-top: 28px; border-top: 1px solid var(--line); }
        .history h2 { margin-bottom: 14px; }
        .history-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 760px; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid var(--line); font-size: 13px; }
        th { color: var(--muted); font-weight: 600; background: #fbfbfd; }
        .history-status { display: inline-block; padding: 5px 8px; border-radius: 5px; color: #fff; background: #20b968; font-size: 12px; }
        .history-status.rejected { background: #ee6b67; }
        @media (max-width: 680px) { .page-shell { width: min(100% - 20px, 980px); margin: 10px auto; } header, main { padding: 20px; } header { align-items: flex-start; flex-direction: column; } .transaction, .form-grid { grid-template-columns: 1fr; } .form-full { grid-column: auto; } .actions { justify-content: stretch; } .actions > * { flex: 1; text-align: center; } }
    </style>
</head>
<body>
    <div class="page-shell">
        <header>
            <div class="brand"><img src="../images/protest.png" alt="FixLine Logo"><span>Finance Officer Dashboard</span></div>
            <a class="back-link" href="financeofficerdashboard.php">Back to Dashboard</a>
        </header>
        <main>
            <h1>Refund</h1>
            <p class="subtitle">You are about to initiate a refund</p>

            <?php if ($message !== ''): ?><div class="notice <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

            <?php if ($selectedPayment): ?>
                <form method="post">
                    <div class="payment-picker">
                        <label for="invoice-select">Customer payment</label>
                        <select id="invoice-select" name="invoice_id" onchange="window.location.href='refunds.php?invoice=' + encodeURIComponent(this.value)">
                            <?php foreach ($payments as $payment): ?>
                                <option value="<?php echo htmlspecialchars($payment['invoice_id']); ?>" <?php echo $payment['invoice_id'] === $selectedInvoice ? 'selected' : ''; ?>><?php echo htmlspecialchars($payment['invoice_id'] . ' - ' . $payment['customer_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="transaction">
                        <div class="transaction-row"><span>Customer ID</span><strong><?php echo htmlspecialchars($selectedPayment['customer_id']); ?></strong></div>
                        <div class="transaction-row"><span>Customer Name</span><strong><?php echo htmlspecialchars($selectedPayment['customer_name']); ?></strong></div>
                        <div class="transaction-row"><span>Customer Email</span><strong><?php echo htmlspecialchars($selectedPayment['customer_email'] ?: 'Not provided'); ?></strong></div>
                        <div class="transaction-row"><span>Transaction Amount</span><strong>$<?php echo htmlspecialchars(number_format((float) $selectedPayment['amount'], 2)); ?></strong></div>
                    </div>

                    <div class="amount-box">
                        <label for="amount">Refund amount</label>
                        <input id="amount" type="text" value="$<?php echo htmlspecialchars(number_format((float) $selectedPayment['amount'], 2)); ?>" readonly>
                        <p class="full-refund-note">Only a full refund of the original payment amount can be approved.</p>
                    </div>
                    <p class="decision-note">Reject the request if the refund should not be approved. Rejected requests cannot be refunded from this page.</p>
                    <div class="actions"><a class="cancel" href="financeofficerdashboard.php">Cancel</a><button class="reject" type="submit" name="reject_refund">Reject Refund</button><button class="refund" type="submit" name="approve_refund">Approve Full Refund</button></div>
                </form>
            <?php else: ?>
                <div class="empty-state">No approved customer payments are currently available for refund.</div>
            <?php endif; ?>

            <section class="history">
                <h2>Refund Decision History</h2>
                <?php if ($refundHistory): ?>
                    <div class="history-wrap">
                        <table>
                            <thead><tr><th>Customer ID</th><th>Customer</th><th>Email</th><th>Invoice</th><th>Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($refundHistory as $refund): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($refund['customer_id']); ?></td>
                                        <td><?php echo htmlspecialchars($refund['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($refund['customer_email'] ?: 'Not provided'); ?></td>
                                        <td><?php echo htmlspecialchars($refund['invoice_id']); ?></td>
                                        <td>$<?php echo htmlspecialchars(number_format((float) $refund['amount'], 2)); ?></td>
                                        <td><span class="history-status <?php echo strtolower($refund['status']); ?>"><?php echo htmlspecialchars($refund['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">No refund decisions have been recorded yet.</div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
