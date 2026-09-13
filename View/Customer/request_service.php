<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$requestMessage = $_SESSION['request_message'] ?? null;
$requestError = $_SESSION['request_error'] ?? null;
unset($_SESSION['request_message'], $_SESSION['request_error']);

$services = $services ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Request a Service</title>
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
            <h1>Request a Service</h1>
            <a href="/FixLine/View/Customer/customer_dashboard.php">Back to Dashboard</a>
        </div>

        <?php if ($requestMessage !== null): ?>
            <div class="notice"><?= htmlspecialchars($requestMessage) ?></div>
        <?php endif; ?>
        <?php if ($requestError !== null): ?>
            <div class="notice"><?= htmlspecialchars($requestError) ?></div>
        <?php endif; ?>

        <form action="/FixLine/cindex.php?action=request_service" method="post">
            <label>
                Service
                <select name="service_id" required>
                    <option value="">Choose a service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= (int) $service['service_id'] ?>" data-provider-id="<?= (int) $service['provider_id'] ?>">
                            <?= htmlspecialchars($service['service_name']) ?> - <?= htmlspecialchars($service['provider_name']) ?> (Tk <?= htmlspecialchars(number_format((float) $service['price'], 2)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <input type="hidden" name="provider_id" id="provider-id">

            <label>
                Request title
                <input type="text" name="title" maxlength="150" required>
            </label>

            <label>
                Category
                <input type="text" name="category" maxlength="100" placeholder="e.g. Plumber" required>
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

            <label>
                Service date
                <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
            </label>

            <button type="submit">Send Request</button>
        </form>
    </main>
    <script>
        const serviceSelect = document.querySelector('select[name="service_id"]');
        const providerInput = document.getElementById('provider-id');
        serviceSelect.addEventListener('change', () => {
            providerInput.value = serviceSelect.selectedOptions[0]?.dataset.providerId || '';
        });
    </script>
</body>
</html>
