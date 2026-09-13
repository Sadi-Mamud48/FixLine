<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$jobMessage = $_SESSION['job_message'] ?? null;
$jobError = $_SESSION['job_error'] ?? null;
unset($_SESSION['job_message'], $_SESSION['job_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Post a Job</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px; font-family: Arial, sans-serif; background: #5b3dcc; color: #fff; }
        .page { max-width: 760px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 24px; }
        .page-header a { color: #fff; text-decoration: none; }
        .notice { margin-bottom: 18px; padding: 12px; background: #fff; color: #222; border-radius: 4px; }
        form { display: grid; gap: 16px; padding: 24px; background: #fff; color: #222; border-radius: 8px; }
        label { display: grid; gap: 6px; font-weight: 700; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #bbb; font: inherit; }
        textarea { min-height: 110px; resize: vertical; }
        button { justify-self: start; padding: 10px 18px; border: 0; background: #4524a9; color: #fff; cursor: pointer; font-weight: 700; }
    </style>
</head>
<body>
    <main class="page">
        <div class="page-header">
            <h1>Post a Job</h1>
            <a href="/FixLine/View/Customer/customer_dashboard.php">Back to Dashboard</a>
        </div>

        <?php if ($jobMessage !== null): ?>
            <div class="notice"><?= htmlspecialchars($jobMessage) ?></div>
        <?php endif; ?>
        <?php if ($jobError !== null): ?>
            <div class="notice"><?= htmlspecialchars($jobError) ?></div>
        <?php endif; ?>

        <form action="/FixLine/cindex.php?action=post_job" method="post">
            <label>
                Job title
                <input type="text" name="title" maxlength="150" required placeholder="e.g. Repair leaking kitchen pipe">
            </label>

            <label>
                Category
                <select name="category" required>
                    <option value="">Choose a category</option>
                    <option value="plumber">Plumber</option>
                    <option value="electrician">Electrician</option>
                    <option value="painter">Painter</option>
                    <option value="appliance">Appliance repairer</option>
                </select>
            </label>

            <label>
                Description
                <textarea name="description" required></textarea>
            </label>

            <label>
                Location
                <input type="text" name="location" maxlength="150">
            </label>

            <label>
                Budget (Tk)
                <input type="number" name="budget" min="0" step="0.01">
            </label>

            <button type="submit">Post Job</button>
        </form>
    </main>
</body>
</html>
