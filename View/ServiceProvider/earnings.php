<?php
// Expects: $provider, $summary, $earnings — supplied by ServiceProviderController::earnings()
$activePage = 'earnings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine — My Earnings</title>
    <link rel="stylesheet" href="View/css/style.css">
</head>
<body>
<div class="fx-page">
    <div class="fx-shell">

        <?php include __DIR__ . '/partials/header.php'; ?>

        <div style="padding:36px;">

            <h2 style="color:#fff;margin-top:0;">Earnings Overview</h2>

            <div class="fx-stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:32px;">
                <div class="fx-card">
                    <h3>Total Earned</h3>
                    <div class="fx-stat">Tk <?= number_format((float) ($summary['total_earned'] ?? 0), 2) ?></div>
                </div>
                <div class="fx-card">
                    <h3>Paid Out</h3>
                    <div class="fx-stat">Tk <?= number_format((float) ($summary['total_paid'] ?? 0), 2) ?></div>
                </div>
                <div class="fx-card">
                    <h3>Pending Payout</h3>
                    <div class="fx-stat">Tk <?= number_format((float) ($summary['total_pending'] ?? 0), 2) ?></div>
                </div>
            </div>

            <table class="fx-table">
                <thead>
                <tr>
                    <th>Job</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($earnings)): ?>
                    <tr><td colspan="4">No earnings recorded yet. Completed jobs will appear here.</td></tr>
                <?php else: ?>
                    <?php foreach ($earnings as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['job_title'] ?? 'General payout') ?></td>
                            <td>Tk <?= number_format((float) $row['amount'], 2) ?></td>
                            <td><span class="fx-badge fx-badge-<?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($row['earned_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>
</body>
</html>
