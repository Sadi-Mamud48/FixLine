<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$services = $services ?? [];
$bookingMessage = $_SESSION['booking_message'] ?? null;
unset($_SESSION['booking_message']);
$bookingError = $_SESSION['booking_error'] ?? null;
unset($_SESSION['booking_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FixLine - Search Results</title>
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			padding: 32px;
			font-family: Arial, sans-serif;
			background: #5b3dcc;
			color: #fff;
		}
		a { color: #fff; }
		.page-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 20px;
			margin-bottom: 28px;
		}
		.page-header a { text-decoration: none; }
		.results {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
			gap: 20px;
		}
		.service-card {
			background: #fff;
			color: #222;
			padding: 20px;
			border-radius: 8px;
		}
		.service-card h2 { margin-top: 0; }
		.service-card p { min-height: 48px; }
		.service-card form { display: grid; gap: 10px; }
		.service-card input,
		.service-card button {
			padding: 10px;
			border: 1px solid #bbb;
			border-radius: 4px;
		}
		.service-card button {
			background: #5b3dcc;
			color: #fff;
			cursor: pointer;
		}
		.empty { background: #fff; color: #222; padding: 20px; border-radius: 8px; }
	</style>
</head>
<body>
	<?php if ($bookingMessage !== null): ?>
		<script>alert(<?= json_encode($bookingMessage) ?>);</script>
	<?php endif; ?>
	<?php if ($bookingError !== null): ?>
		<div class="empty"><strong>Booking failed:</strong> <?= htmlspecialchars($bookingError) ?></div>
	<?php endif; ?>

	<div class="page-header">
		<h1>Available Services</h1>
		<a href="View/Customer/customer_dashboard.php">Back to Dashboard</a>
	</div>

	<?php if (empty($services)): ?>
		<div class="empty">No services found. Add services to the services table and try again.</div>
	<?php else: ?>
		<div class="results">
			<?php foreach ($services as $service): ?>
				<article class="service-card">
					<h2><?= htmlspecialchars($service['service_name']) ?></h2>
					<p><?= htmlspecialchars($service['description'] ?? '') ?></p>
					<p><strong>Price:</strong> <?= htmlspecialchars($service['price']) ?></p>
					<form action="/FixLine/index.php?action=book" method="post">
						<input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
						<input type="hidden" name="category" value="<?= htmlspecialchars($service['category'] ?? '') ?>">
						<label>
							Booking date
							<input type="date" name="booking_date" required>
						</label>
						<button type="submit">Book Service</button>
					</form>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</body>
</html>
