<?php
// FixLine/View/Moderator/complaints.php
require_once __DIR__ . '/../../Model/Moderator.php';
$moderatorModel = new Moderator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_update'], $_POST['complaint_id'])) {
    $moderatorModel->updateComplaintStatus($_POST['complaint_id'], $_POST['status_update']);
    header("Location: complaints.php");
    exit();
}

$complaints = $moderatorModel->getComplaints();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Complaints - FixLine</title>
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
    .btn-resolve { background: #3182ce; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .btn-escalate { background: #dd6b20; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
    .badge-open { background: #feebc8; color: #c05621; }
    .badge-resolved { background: #c6f6d5; color: #22543d; }
    .badge-escalated { background: #fed7d7; color: #9b2c2c; }
  </style>
</head>
<body>
<div class="dashboard-wrapper">
  <div class="dashboard-card">
    <div class="dashboard-main">
      <header class="header">
        <h2>Customer Complaints & Tickets</h2>
        <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
      </header>
      <div class="content-area">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Provider</th>
              <th>Issue Description</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($complaints)): ?>
              <?php foreach ($complaints as $c): ?>
                <tr>
                  <td>#<?= htmlspecialchars($c['id']); ?></td>
                  <td><?= htmlspecialchars($c['customer_name']); ?></td>
                  <td><?= htmlspecialchars($c['provider_name']); ?></td>
                  <td><?= htmlspecialchars($c['issue_description']); ?></td>
                  <td><span class="badge badge-<?= htmlspecialchars($c['status']); ?>"><?= htmlspecialchars($c['status']); ?></span></td>
                  <td>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="complaint_id" value="<?= $c['id']; ?>">
                      <button type="submit" name="status_update" value="resolved" class="btn-resolve">Resolve</button>
                      <button type="submit" name="status_update" value="escalated_to_admin" class="btn-escalate">Escalate</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No complaints found in the database.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>