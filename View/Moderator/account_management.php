<?php
// FixLine/View/Moderator/account_management.php
require_once __DIR__ . '/../../Model/Moderator.php';
$moderatorModel = new Moderator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suspend_id'])) {
    $moderatorModel->updateProviderStatus($_POST['suspend_id'], 'rejected');
    header('Location: /FixLine/moderator.php?action=account_management');
    exit();
}

$approvedProviders = $moderatorModel->getProvidersByStatus('approved');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Management - FixLine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
    body { background-color: #f4f4f9; display: flex; justify-content: center; padding: 30px 20px; }
    .dashboard-wrapper { width: 100%; max-width: 1050px; }
    .dashboard-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .dashboard-main { background: linear-gradient(135deg, #6b46c1 0%, #553c9a 100%); padding: 25px 35px 40px; color: white; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
    .back-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; }
    .content-area { background: rgba(255, 255, 255, 0.95); border-radius: 12px; padding: 20px; color: #333; margin-top: 10px; }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th, .data-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
    .btn-suspend { background: #e53e3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .status-active { color: #0cc631; font-weight: bold; }
  </style>
</head>
<body>
<div class="dashboard-wrapper">
  <div class="dashboard-card">
    <div class="dashboard-main">
      <header class="header">
        <h2>Service Provider Management</h2>
        <a href="/FixLine/moderator.php?action=dashboard" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
      </header>
      <div class="content-area">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Technician Name</th>
              <th>Email</th>
              <th>Service</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($approvedProviders)): ?>
              <?php foreach ($approvedProviders as $p): ?>
                <tr>
                  <td>#<?= htmlspecialchars($p['id']); ?></td>
                  <td><?= htmlspecialchars($p['name']); ?></td>
                  <td><?= htmlspecialchars($p['email']); ?></td>
                  <td><?= htmlspecialchars($p['service_type']); ?></td>
                  <td><span class="status-active">Verified</span></td>
                  <td>
                    <form method="POST">
                      <input type="hidden" name="suspend_id" value="<?= $p['id']; ?>">
                      <button type="submit" class="btn-suspend">Revoke</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No approved active providers currently on platform.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
