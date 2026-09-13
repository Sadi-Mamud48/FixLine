<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Payment.php';

$payments = [];
$databaseMessage = '';
$invoice = trim($_GET['invoice'] ?? '');
$startDate = trim($_GET['start_date'] ?? '');
$endDate = trim($_GET['end_date'] ?? '');

try {
    $paymentModel = new Payment();
    $payments = $paymentModel->getAllTransactions($invoice, $startDate, $endDate);
} catch (Throwable $exception) {
    $databaseMessage = 'Customer payment records are temporarily unavailable. Please check the database connection.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - FixLine</title>
    <style>
        :root {
            --purple: #5b3dcc;
            --deep-purple: #2c167e;
            --ink: #25243a;
            --muted: #77758a;
            --line: #ecebf2;
            --green: #20b968;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f7f7fb;
            color: var(--ink);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-shell {
            width: min(1180px, calc(100% - 40px));
            margin: 24px auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px 26px;
            color: #fff;
            background: var(--purple);
            border-radius: 18px 18px 0 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 19px;
            font-weight: 600;
            white-space: nowrap;
        }

        .brand img { width: 38px; height: 38px; object-fit: contain; }

        .back-link {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid rgba(255,255,255,.55);
            padding: 9px 14px;
            border-radius: 8px;
        }

        .back-link:hover { background: rgba(255,255,255,.12); }

        main {
            padding: 34px;
            background: #fff;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 12px 30px rgba(44, 22, 126, .12);
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 { margin: 0; font-size: 28px; }
        .page-heading p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }

        .add-button {
            border: 0;
            border-radius: 9px;
            padding: 12px 18px;
            color: #fff;
            background: #4c6be8;
            font-size: 14px;
            cursor: pointer;
        }

        .filters {
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr auto;
            gap: 14px;
            padding: 18px;
            margin-bottom: 24px;
            background: #f7f7fb;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        label { display: block; color: var(--muted); font-size: 12px; margin-bottom: 7px; }
        input, select {
            width: 100%;
            min-height: 42px;
            padding: 0 12px;
            border: 1px solid #dedde8;
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            font: inherit;
        }

        .filter-button {
            align-self: end;
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            background: var(--green);
            cursor: pointer;
        }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { padding: 16px 12px; text-align: left; border-bottom: 1px solid var(--line); font-size: 14px; }
        th { color: var(--muted); font-size: 12px; font-weight: 600; background: #fbfbfd; }
        td { color: #4b4a5b; }
        .status { display: inline-block; padding: 7px 10px; border-radius: 5px; color: #fff; background: var(--green); font-size: 12px; }
        .status.pending { background: #e5a52d; }
        .status.refunded { background: #ee6b67; }
        .database-message { padding: 14px 16px; margin-bottom: 20px; border-radius: 8px; color: #765b17; background: #fff7d6; font-size: 14px; }
        .empty-state { padding: 34px 16px; text-align: center; color: var(--muted); }
        .actions { display: flex; gap: 8px; }
        .action-button { border: 1px solid var(--line); border-radius: 6px; padding: 7px 9px; color: var(--deep-purple); background: #fff; cursor: pointer; }

        @media (max-width: 760px) {
            .page-shell { width: min(100% - 20px, 1180px); margin: 10px auto; }
            header, .page-heading { align-items: flex-start; flex-direction: column; }
            main { padding: 20px 14px; }
            .filters { grid-template-columns: 1fr; }
            .filter-button { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header>
            <div class="brand">
                <img src="../images/protest.png" alt="FixLine Logo">
                <span>Finance Officer Dashboard</span>
            </div>
            <a class="back-link" href="financeofficerdashboard.php">Back to Dashboard</a>
        </header>

        <main>
            <div class="page-heading">
                <div>
                    <h1>Payment List</h1>
                    <p>Review and manage customer payment transactions.</p>
                </div>
                <button class="add-button" type="button">+ Add New</button>
            </div>

            <?php if ($databaseMessage !== ''): ?>
                <div class="database-message"><?php echo htmlspecialchars($databaseMessage); ?></div>
            <?php endif; ?>

            <form class="filters" method="get">
                <div>
                    <label for="invoice">Invoice ID</label>
                    <input id="invoice" name="invoice" type="search" placeholder="Search invoice ID">
                </div>
                <div>
                    <label for="start-date">Start Date</label>
                    <input id="start-date" name="start_date" type="date">
                </div>
                <div>
                    <label for="end-date">End Date</label>
                    <input id="end-date" name="end_date" type="date">
                </div>
                <button class="filter-button" type="submit">Search</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" aria-label="Select all payments"></th>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $index => $payment): ?>
                            <tr>
                                <td><input type="checkbox" aria-label="Select payment <?php echo $index + 1; ?>"></td>
                                <td><?php echo htmlspecialchars($payment['invoice_id']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($payment['payment_date']))); ?></td>
                                <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format((float) $payment['amount'], 2)); ?></td>
                                <td><span class="status <?php echo strtolower($payment['status']); ?>"><?php echo htmlspecialchars($payment['status']); ?></span></td>
                                <td class="actions">
                                    <button class="action-button" type="button" title="View payment">View</button>
                                    <button class="action-button" type="button" title="Edit payment">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$payments): ?>
                            <tr>
                                <td class="empty-state" colspan="8">No payment records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
