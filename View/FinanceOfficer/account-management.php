<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Account.php';

$accounts = [];
$message = '';
$messageType = '';
$search = trim($_GET['search'] ?? '');
$type = trim($_GET['type'] ?? '');

try {
    $accountModel = new Account();
    $accounts = $accountModel->getAll($search, $type);
} catch (Throwable $exception) {
    $message = 'Account records are temporarily unavailable. Please check the database connection.';
    $messageType = 'error';
}

$activeCount = count(array_filter($accounts, static function ($account) { return $account['status'] === 'Active'; }));
$blockedCount = count(array_filter($accounts, static function ($account) { return $account['status'] === 'Blocked'; }));
$totalPayments = array_sum(array_map(static function ($account) { return (float) ($account['last_payment'] ?? 0); }, $accounts));
$totalPayouts = array_sum(array_map(static function ($account) { return (float) ($account['last_payout'] ?? 0); }, $accounts));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management - FixLine</title>
    <style>
        :root { --purple: #5b3dcc; --blue: #5865e8; --ink: #242335; --muted: #79788b; --line: #ececf3; --soft: #f7f7fb; --green: #22b978; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f3f4fa; color: var(--ink); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .app { min-height: 100vh; }
        .content { width: 100%; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px 28px; background: #fff; border-bottom: 1px solid var(--line); }
        .profile { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; }
        .profile img { width: 34px; height: 34px; object-fit: contain; border-radius: 50%; }
        .top-actions { display: flex; gap: 18px; color: var(--ink); font-size: 19px; }
        .dashboard-link { padding: 10px 14px; color: var(--blue); background: #e9ecff; border-radius: 7px; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; }
        .dashboard-link:hover { background: #dfe4ff; }
        main { padding: 30px; }
        .heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 23px; }
        h1 { margin: 0; font-size: 26px; }
        .crumbs { margin-top: 8px; color: var(--muted); font-size: 12px; }
        .crumbs strong { color: var(--blue); }
        .heading-actions { display: flex; gap: 10px; }
        .heading-actions button { border: 0; border-radius: 7px; padding: 10px 14px; color: #fff; background: var(--blue); cursor: pointer; font: inherit; }
        .heading-actions button:first-child { color: var(--blue); background: #e9ecff; }
        .overview { display: grid; grid-template-columns: 230px repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
        .profile-card, .stat-card, .account-table { background: #fff; border-radius: 9px; box-shadow: 0 2px 9px rgba(30, 30, 70, .04); }
        .profile-card { overflow: hidden; }
        .profile-cover { height: 76px; background: var(--blue); }
        .avatar { display: block; width: 72px; height: 72px; margin: -36px auto 8px; padding: 5px; border-radius: 50%; background: #fff; object-fit: contain; }
        .profile-card h2 { margin: 0; text-align: center; font-size: 15px; }
        .profile-card p { margin: 5px 0 18px; color: var(--muted); text-align: center; font-size: 11px; }
        .stat-card { padding: 18px; min-height: 128px; }
        .stat-label { color: var(--muted); font-size: 12px; }
        .stat-value { margin-top: 13px; font-size: 24px; font-weight: 700; }
        .stat-change { display: inline-block; margin-top: 10px; padding: 3px 6px; color: var(--green); background: #e6f8f0; border-radius: 10px; font-size: 11px; }
        .notice { padding: 12px 14px; margin-bottom: 18px; border-radius: 7px; font-size: 13px; }
        .notice.success { color: #176a3a; background: #e5f8ed; }
        .notice.error { color: #8b3f32; background: #fff0ed; }
        .account-table { padding: 20px; overflow: hidden; }
        .table-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
        h2 { margin: 0; font-size: 17px; }
        .table-heading input { width: 200px; padding: 9px 12px; border: 1px solid var(--line); border-radius: 6px; font: inherit; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 700px; border-collapse: collapse; }
        th, td { padding: 14px 10px; text-align: left; border-bottom: 1px solid var(--line); font-size: 13px; }
        th { color: var(--muted); background: #fbfbfd; font-size: 11px; font-weight: 600; }
        td { color: #515064; }
        .status { display: inline-block; padding: 5px 8px; border-radius: 12px; color: var(--green); background: #e6f8f0; font-size: 11px; }
        .status.blocked { color: #bb4b43; background: #ffebe9; }
        .row-actions { color: var(--blue); white-space: nowrap; }
        .row-actions button { margin-right: 7px; border: 0; color: inherit; background: none; cursor: pointer; }
        .account-overview { margin-top: 22px; }
        .account-overview > h2 { margin-bottom: 14px; }
        .account-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .account-card { padding: 20px; background: #fff; border: 1px solid var(--line); border-left: 4px solid var(--blue); border-radius: 9px; box-shadow: 0 2px 9px rgba(30, 30, 70, .04); }
        .account-card.provider { border-left-color: var(--purple); }
        .account-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 16px; }
        .account-card-header h3 { margin: 0 0 5px; font-size: 16px; }
        .account-card-header p { margin: 0; color: var(--muted); font-size: 12px; }
        .account-details { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding-top: 15px; border-top: 1px solid var(--line); }
        .account-detail span { display: block; margin-bottom: 4px; color: var(--muted); font-size: 11px; }
        .account-detail strong { font-size: 13px; font-weight: 600; }
        @media (max-width: 900px) { .overview { grid-template-columns: repeat(2, 1fr); } .profile-card { grid-column: span 2; } }
        @media (max-width: 600px) { header, main { padding: 18px; } header { flex-wrap: wrap; } .heading { align-items: flex-start; flex-direction: column; } .overview { grid-template-columns: 1fr; } .profile-card { grid-column: auto; } .table-heading { align-items: flex-start; flex-direction: column; gap: 12px; } .table-heading input { width: 100%; } .account-details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="app">
        <div class="content">
            <header>
                <div class="profile"><img src="/FixLine/View/images/protest.png" alt="Finance Officer"><span>Finance Officer</span></div>
                <div class="top-actions"><a class="dashboard-link" href="/FixLine/index.php?action=finance_dashboard">Back to Dashboard</a><a class="dashboard-link" href="/FixLine/index.php?action=logout">Logout</a><span title="Notifications">♧</span></div>
            </header>
            <main>
                <div class="heading">
                    <div><h1>Account Management</h1><div class="crumbs"><strong>Dashboard</strong> &nbsp;›&nbsp; Customer List &nbsp;›&nbsp; Account Management</div></div>
                    <div class="heading-actions"><button type="button">Export</button><button type="button">+ Add Customer</button></div>
                </div>
                <?php if ($message !== ''): ?><div class="notice <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                <section class="overview">
                    <div class="profile-card"><div class="profile-cover"></div><img class="avatar" src="/FixLine/View/images/protest.png" alt="Account overview"><h2>Customer &amp; Provider Accounts</h2><p>Finance account overview</p></div>
                    <div class="stat-card"><div class="stat-label">Total Accounts</div><div class="stat-value"><?php echo count($accounts); ?></div><span class="stat-change">Live</span></div>
                    <div class="stat-card"><div class="stat-label">Active / Blocked</div><div class="stat-value"><?php echo $activeCount; ?> / <?php echo $blockedCount; ?></div><span class="stat-change">Status</span></div>
                    <div class="stat-card"><div class="stat-label">Last Payments</div><div class="stat-value">$<?php echo number_format($totalPayments, 2); ?></div><span class="stat-change">Customers</span></div>
                    <div class="stat-card"><div class="stat-label">Last Payouts</div><div class="stat-value">$<?php echo number_format($totalPayouts, 2); ?></div><span class="stat-change"><?php echo $blockedCount; ?> blocked</span></div>
                </section>
                <section class="account-table">
                    <div class="table-heading"><h2>Customers &amp; Service Providers</h2><form method="get"><input type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search accounts" aria-label="Search accounts"></form></div>
                    <div class="table-wrap"><table><thead><tr><th>Type</th><th>Name</th><th>ID</th><th>Email</th><th>Last Payment</th><th>Last Payout</th><th>Last Login</th><th>Status</th></tr></thead><tbody>
                        <?php foreach ($accounts as $account): ?>
                            <tr><td><?php echo htmlspecialchars($account['account_type']); ?></td><td><?php echo htmlspecialchars($account['name']); ?></td><td><?php echo htmlspecialchars($account['account_id']); ?></td><td><?php echo htmlspecialchars($account['email']); ?></td><td><?php echo $account['last_payment'] !== null ? '$' . number_format((float) $account['last_payment'], 2) : '-'; ?></td><td><?php echo $account['last_payout'] !== null ? '$' . number_format((float) $account['last_payout'], 2) : '-'; ?></td><td><?php echo $account['last_login'] ? htmlspecialchars(date('d M Y', strtotime($account['last_login']))) : '-'; ?></td><td><span class="status <?php echo strtolower($account['status']); ?>"><?php echo htmlspecialchars($account['status']); ?></span></td></tr>
                        <?php endforeach; ?>
                        <?php if (!$accounts): ?><tr><td colspan="8" class="empty-state">No customer or provider accounts found.</td></tr><?php endif; ?>
                    </tbody></table></div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
