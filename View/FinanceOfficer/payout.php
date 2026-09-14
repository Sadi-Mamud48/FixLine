<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Payment.php';

$payouts = [];
$databaseMessage = '';
$provider = trim($_GET['provider'] ?? '');
$startDate = trim($_GET['start_date'] ?? '');
$endDate = trim($_GET['end_date'] ?? '');

try {
    $paymentModel = new Payment();
    $payouts = $paymentModel->getProviderPayouts($provider, $startDate, $endDate);
} catch (Throwable $exception) {
    $databaseMessage = 'Provider payout records are temporarily unavailable. Please check the database connection.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payouts - FixLine</title>
    <style>
        :root { --purple: #5b3dcc; --deep-purple: #2c167e; --ink: #25243a; --muted: #77758a; --line: #ecebf2; --green: #20b968; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f7f7fb; color: var(--ink); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .page-shell { width: min(1180px, calc(100% - 40px)); margin: 24px auto; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 18px 26px; color: #fff; background: var(--purple); border-radius: 18px 18px 0 0; }
        .brand { display: flex; align-items: center; gap: 12px; font-size: 19px; font-weight: 600; }
        .brand img { width: 38px; height: 38px; object-fit: contain; }
        .back-link { color: #fff; text-decoration: none; font-size: 14px; border: 1px solid rgba(255,255,255,.55); padding: 9px 14px; border-radius: 8px; }
        .back-link:hover { background: rgba(255,255,255,.12); }
        main { padding: 34px; background: #fff; border-radius: 0 0 18px 18px; box-shadow: 0 12px 30px rgba(44, 22, 126, .12); }
        .page-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; }
        .page-heading p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }
        .summary { padding: 12px 16px; color: var(--deep-purple); background: #f0edff; border-radius: 8px; font-size: 14px; }
        .filters { display: grid; grid-template-columns: 1.1fr 1fr 1fr auto; gap: 14px; padding: 18px; margin-bottom: 24px; background: #f7f7fb; border: 1px solid var(--line); border-radius: 12px; }
        label { display: block; color: var(--muted); font-size: 12px; margin-bottom: 7px; }
        input { width: 100%; min-height: 42px; padding: 0 12px; border: 1px solid #dedde8; border-radius: 8px; background: #fff; color: var(--ink); font: inherit; }
        .filter-button { align-self: end; min-height: 42px; padding: 0 18px; border: 0; border-radius: 8px; color: #fff; background: var(--green); cursor: pointer; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th, td { padding: 16px 12px; text-align: left; border-bottom: 1px solid var(--line); font-size: 14px; }
        th { color: var(--muted); font-size: 12px; font-weight: 600; background: #fbfbfd; }
        td { color: #4b4a5b; }
        .status { display: inline-block; padding: 7px 10px; border-radius: 5px; color: #fff; background: var(--green); font-size: 12px; }
        .status.pending { background: #e5a52d; }
        .status.failed { background: #ee6b67; }
        .empty-state { padding: 34px 16px; text-align: center; color: var(--muted); }
        .database-message { padding: 14px 16px; margin-bottom: 20px; border-radius: 8px; color: #765b17; background: #fff7d6; font-size: 14px; }
        @media (max-width: 760px) { .page-shell { width: min(100% - 20px, 1180px); margin: 10px auto; } header, .page-heading { align-items: flex-start; flex-direction: column; } main { padding: 20px 14px; } .filters { grid-template-columns: 1fr; } .filter-button { width: 100%; } }
    </style>
</head>
<body>
    <div class="page-shell">
        <header>
            <div class="brand"><img src="/FixLine/View/images/protest.png" alt="FixLine Logo"><span>Finance Officer Dashboard</span></div>
            <div><a class="back-link" href="/FixLine/index.php?action=finance_dashboard">Back to Dashboard</a> <a class="back-link" href="/FixLine/index.php?action=logout">Logout</a></div>
        </header>
        <main>
            <div class="page-heading">
                <div><h1>Provider Payouts</h1><p>Track payments received by service providers.</p></div>
                <div class="summary">Paid payouts: <?php echo count(array_filter($payouts, static function ($payout) { return $payout['status'] === 'Paid'; })); ?></div>
            </div>
            <?php if ($databaseMessage !== ''): ?><div class="database-message"><?php echo htmlspecialchars($databaseMessage); ?></div><?php endif; ?>
            <form class="filters" method="get">
                <div><label for="provider">Service Provider</label><input id="provider" name="provider" type="search" value="<?php echo htmlspecialchars($provider); ?>" placeholder="Search provider"></div>
                <div><label for="start-date">Start Date</label><input id="start-date" name="start_date" type="date" value="<?php echo htmlspecialchars($startDate); ?>"></div>
                <div><label for="end-date">End Date</label><input id="end-date" name="end_date" type="date" value="<?php echo htmlspecialchars($endDate); ?>"></div>
                <button class="filter-button" type="submit">Search</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Provider</th><th>Job ID</th><th>Invoice ID</th><th>Amount</th><th>Method</th><th>Payment Date</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($payouts as $payout): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payout['provider_name']); ?></td>
                                <td><?php echo htmlspecialchars($payout['job_id']); ?></td>
                                <td><?php echo htmlspecialchars($payout['invoice_id']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format((float) $payout['amount'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($payout['payout_method']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($payout['payout_date']))); ?></td>
                                <td><span class="status <?php echo strtolower($payout['status']); ?>"><?php echo htmlspecialchars($payout['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$payouts): ?><tr><td class="empty-state" colspan="7">No provider payout records found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
