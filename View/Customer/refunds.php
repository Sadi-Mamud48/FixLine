<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$refundMessage = $_SESSION['refund_message'] ?? null;
$refundError = $_SESSION['refund_error'] ?? null;
unset($_SESSION['refund_message'], $_SESSION['refund_error']);

if (!isset($payments)) {
    require_once __DIR__ . '/../../Config/Database.php';
    require_once __DIR__ . '/../../Model/Customer.php';

    $database = new Database();
    $customerModel = new Customer($database->getConnection());
    $userId = $_SESSION['user_id'] ?? 1;
    $payments = $customerModel->getPaidPayments($userId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Refund Request</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: Arial, sans-serif;
            background: #5b3dcc;
            color: #fff;
        }
        a { color: #fff; }
        .page { max-width: 760px; margin: 0 auto; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-header a { text-decoration: none; }
        .notice {
            margin-bottom: 18px;
            padding: 12px;
            background: #fff;
            color: #222;
            border-radius: 4px;
        }
        .refund-card {
            margin-bottom: 16px;
            padding: 20px;
            background: #fff;
            color: #222;
            border-radius: 8px;
        }
        .refund-card h2 { margin-top: 0; }
        .refund-card form { display: grid; gap: 10px; }
        .refund-card textarea { min-height: 80px; padding: 10px; resize: vertical; }
        .refund-card button {
            justify-self: start;
            padding: 10px 18px;
            border: 0;
            background: #4524a9;
            color: #fff;
            cursor: pointer;
        }
        .empty { padding: 20px; background: #fff; color: #222; border-radius: 8px; }
    </style>
</head>
<body>
    <main class="page">
        <div class="page-header">
            <h1>Refund Request</h1>
            <a href="/FixLine/View/Customer/customer_dashboard.php">Back to Dashboard</a>
        </div>

        <?php if ($refundMessage !== null): ?>
            <div class="notice"><?= htmlspecialchars($refundMessage) ?></div>
        <?php endif; ?>
        <?php if ($refundError !== null): ?>
            <div class="notice"><?= htmlspecialchars($refundError) ?></div>
        <?php endif; ?>

        <?php if (empty($payments)): ?>
            <div class="empty">You have no paid services available for a refund request.</div>
        <?php else: ?>
            <?php foreach ($payments as $payment): ?>
                <article class="refund-card">
                    <h2><?= htmlspecialchars($payment['service_name']) ?></h2>
                    <p><strong>Amount paid:</strong> <?= htmlspecialchars(number_format((float) $payment['amount'], 2)) ?></p>
                    <p><strong>Paid on:</strong> <?= htmlspecialchars($payment['paid_at']) ?></p>
                    <form action="/FixLine/cindex.php?action=refunds" method="post">
                        <input type="hidden" name="payment_id" value="<?= (int) $payment['payment_id'] ?>">
                        <label>
                            Reason for refund
                            <textarea name="reason" required></textarea>
                        </label>
                        <button type="submit">Request Refund</button>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
