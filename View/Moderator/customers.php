<?php
// FixLine/View/Moderator/customers.php
require_once __DIR__ . '/../../Model/Moderator.php';

$moderatorModel = new Moderator();
$customers = $moderatorModel->getCustomers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers Management - FixLine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
    body { background-color: #f4f4f9; display: flex; justify-content: center; padding: 30px 20px; }
    
    .dashboard-wrapper { position: relative; width: 100%; max-width: 1050px; }
    .dashboard-shadow { position: absolute; top: 15px; left: 15px; width: 100%; height: 100%; background-color: #5c4ca8; border-radius: 24px; z-index: 1; }
    .dashboard-card { position: relative; z-index: 2; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
    
    .dashboard-main { background: linear-gradient(135deg, #6b46c1 0%, #553c9a 100%); padding: 25px 35px 40px; min-height: 460px; color: white; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
    .brand-title { display: flex; align-items: center; gap: 12px; font-size: 20px; font-weight: 600; text-decoration: none; color: white; }
    .back-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; transition: 0.3s; }
    .back-btn:hover { background: rgba(255,255,255,0.4); }

    .content-area { background: rgba(255, 255, 255, 0.95); border-radius: 12px; padding: 20px; color: #333; margin-top: 10px; }
    .content-area h2 { margin-bottom: 15px; color: #553c9a; font-size: 22px; }

    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th, .data-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
    .data-table th { background-color: #f7fafc; color: #4a5568; font-weight: 600; }
    .data-table tr:hover { background-color: #f8fafc; }
    
    .badge-id { background: #e9d8fd; color: #553c9a; padding: 4px 8px; border-radius: 6px; font-weight: bold; font-size: 13px; }
  </style>
</head>
<body>

<div class="dashboard-wrapper">
  <div class="dashboard-shadow"></div>
  <div class="dashboard-card">
    <div class="dashboard-main">
      <header class="header">
        <a href="index.php" class="brand-title">
          <i class="fa-solid fa-screwdriver-wrench"></i>
          <span>Moderators Dashboard</span>
        </a>
        <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
      </header>

      <div class="content-area">
        <h2>Registered Customers (Database View)</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Gmail</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($customers)): ?>
              <?php foreach ($customers as $customer): ?>
                <tr>
                  <td><span class="badge-id">#<?= htmlspecialchars($customer['id']); ?></span></td>
                  <td><?= htmlspecialchars($customer['name']); ?></td>
                  <td><?= htmlspecialchars($customer['gmail']); ?></td>
                  <td><?= htmlspecialchars($customer['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4">No customer records found in database.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>