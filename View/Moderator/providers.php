<?php
// FixLine/View/Moderator/providers.php
require_once __DIR__ . '/../../Model/Moderator.php';
$moderatorModel = new Moderator();

// Handle Approve / Reject Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'], $_POST['provider_id'])) {
    $moderatorModel->updateProviderStatus($_POST['provider_id'], $_POST['action_type']);
    header("Location: /FixLine/index.php?action=moderator_providers");
    exit();
}

$pendingProviders = $moderatorModel->getProvidersByStatus('pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Applications - FixLine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
    body { background-color: #f4f4f9; display: flex; justify-content: center; padding: 30px 20px; }
    .dashboard-wrapper { width: 100%; max-width: 1050px; }
    .dashboard-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .dashboard-main { background: linear-gradient(135deg, #6b46c1 0%, #5b4c9a 100%); padding: 25px 35px 40px; color: white; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
    .back-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; }
    .content-area { background: rgba(255, 255, 255, 0.95); border-radius: 12px; padding: 20px; color: #333; margin-top: 10px; }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th, .data-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
    .btn-approve { background: #38a169; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .btn-reject { background: #e53e3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
  </style>
</head>
<body>
<div class="dashboard-wrapper">
  <div class="dashboard-card">
    <div class="dashboard-main">
      <header class="header">
        <h2>Provider Applications</h2>
        <a href="/FixLine/index.php?action=moderator_dashboard" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
      </header>
      <div class="content-area">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Service</th>
              <th>Document</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($pendingProviders)): ?>
              <?php foreach ($pendingProviders as $p): ?>
                <tr>
                  <td>#<?= htmlspecialchars($p['id']); ?></td>
                  <td><?= htmlspecialchars($p['name']); ?></td>
                  <td><?= htmlspecialchars($p['email']); ?></td>
                  <td><?= htmlspecialchars($p['service_type']); ?></td>
                  <td><a href="#" style="color:#553c9a; font-weight:bold;"><i class="fa-solid fa-file-pdf"></i> <?= htmlspecialchars($p['credentials_doc']); ?></a></td>
                  <td>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="provider_id" value="<?= $p['id']; ?>">
                      <button type="submit" name="action_type" value="approved" class="btn-approve">Approve</button>
                      <button type="submit" name="action_type" value="rejected" class="btn-reject">Reject</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No pending provider applications found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>