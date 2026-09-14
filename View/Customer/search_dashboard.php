<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$services = $services ?? [];
$jobApplications = $jobApplications ?? [];
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
		<a href="/FixLine/index.php?action=customer_dashboard">Back to Dashboard</a>
	</div>

	<?php if (empty($services) && empty($jobApplications)): ?>
		<div class="empty">No services or provider job applications found in this category.</div>
	<?php else: ?>
		<div class="results">
			<?php foreach ($services as $service): ?>
				<article class="service-card">
					<h2><?= htmlspecialchars($service['service_name']) ?></h2>
					<p><?= htmlspecialchars($service['description'] ?? '') ?></p>
					<p><strong>Provider:</strong> <?= htmlspecialchars($service['provider_name'] ?? '') ?></p>
					<p><strong>Affiliate:</strong> <?= htmlspecialchars($service['affiliate'] ?? 'Not specified') ?></p>
					<p><strong>Cost:</strong> Tk <?= htmlspecialchars(number_format((float) $service['price'], 2)) ?></p>
					<form action="/FixLine/index.php?action=request_service" method="post">
						<input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
						<input type="hidden" name="provider_id" value="<?= (int) $service['provider_id'] ?>">
						<input type="hidden" name="title" value="<?= htmlspecialchars($service['service_name'], ENT_QUOTES) ?>">
						<input type="hidden" name="category" value="<?= htmlspecialchars($service['category'] ?? '', ENT_QUOTES) ?>">
						<input type="hidden" name="return_category" value="<?= htmlspecialchars($_GET['category'] ?? '', ENT_QUOTES) ?>">
						<input type="hidden" name="return_search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES) ?>">
						<label>
							Location
							<input type="text" name="location" maxlength="150" required>
						</label>
						<label>
							Booking date
							<input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
						</label>
						<button type="submit">Request Service</button>
					</form>
				</article>
			<?php endforeach; ?>

			<?php foreach ($jobApplications as $application): ?>
				<article class="service-card">
					<h2><?= htmlspecialchars($application['service_name']) ?></h2>
					<p><?= htmlspecialchars($application['description'] ?? '') ?></p>
					<p><strong>Provider:</strong> <?= htmlspecialchars($application['provider_name']) ?></p>
					<p><strong>Affiliate:</strong> <?= htmlspecialchars($application['affiliate'] ?? 'Not specified') ?></p>
					<p><strong>Proposed cost:</strong> Tk <?= htmlspecialchars(number_format((float) $application['price'], 2)) ?></p>
					<form action="/FixLine/index.php?action=request_service" method="post">
						<input type="hidden" name="application_id" value="<?= (int) $application['application_id'] ?>">
						<input type="hidden" name="provider_id" value="<?= (int) $application['provider_id'] ?>">
						<input type="hidden" name="title" value="<?= htmlspecialchars($application['service_name'], ENT_QUOTES) ?>">
						<input type="hidden" name="category" value="<?= htmlspecialchars($application['category'], ENT_QUOTES) ?>">
						<input type="hidden" name="return_category" value="<?= htmlspecialchars($_GET['category'] ?? '', ENT_QUOTES) ?>">
						<input type="hidden" name="return_search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES) ?>">
						<label>
							Service date
							<input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
						</label>
						<button type="submit">Request This Provider</button>
					</form>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</body>
</html>
