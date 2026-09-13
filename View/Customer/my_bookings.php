<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$bookingMessage = $_SESSION['booking_message'] ?? null;
unset($_SESSION['booking_message']);
$bookingError = $_SESSION['booking_error'] ?? null;
unset($_SESSION['booking_error']);

if (!isset($bookings)) {
	require_once __DIR__ . '/../../Config/Database.php';
	require_once __DIR__ . '/../../Model/Customer.php';

	$database = new Database();
	$customerModel = new Customer($database->getConnection());
	$userId = $_SESSION['user_id'] ?? 1;
	$bookings = $customerModel->getCustomerBookings($userId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FixLine - My Bookings</title>
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
			margin-bottom: 28px;
		}
		.booking-list { display: grid; gap: 16px; }
		.booking {
			background: #fff;
			color: #222;
			padding: 20px;            
			border-radius: 8px;
		}
		.booking h2 { margin-top: 0; }
		.review-form { display: grid; gap: 8px; margin-top: 16px; max-width: 420px; }
		.review-form input,
		.review-form textarea,
		.review-form button { padding: 8px; }
		.review-form button { cursor: pointer; }
		.booking-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-top: 12px; }
		.booking-actions form { margin: 0; }
		.booking-actions button { padding: 8px 12px; border: 0; cursor: pointer; }
		.pay-button { background: #2e8b57; color: #fff; }
		.cancel-button { background: #b22222; color: #fff; }
		.empty { background: #fff; color: #222; padding: 20px; border-radius: 8px; }
	</style>
</head>
<body>
	<?php if ($bookingError !== null): ?>
		<div class="empty"><strong>Booking failed:</strong> <?= htmlspecialchars($bookingError) ?></div>
	<?php endif; ?>

	<?php if ($bookingMessage !== null): ?>
		<script>
			alert(<?= json_encode($bookingMessage) ?>);
		</script>
	<?php endif; ?>

	<div class="page-header">
		<h1>My Bookings</h1>
		<a href="/FixLine/View/Customer/customer_dashboard.php">Back to Dashboard</a>
	</div>

	<?php if (empty($bookings)): ?>
		<div class="empty">You do not have any bookings yet.</div>
	<?php else: ?>
		<div class="booking-list">
			<?php foreach ($bookings as $booking): ?>
				<article class="booking">
					<h2><?= htmlspecialchars($booking['service_name']) ?></h2>
					<p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
					<p><strong>Provider:</strong> <?= htmlspecialchars($booking['provider_name'] ?? 'Service provider') ?></p>
					<p><strong>Affiliate:</strong> <?= htmlspecialchars($booking['affiliate'] ?? 'Not specified') ?></p>
					<p><strong>Status:</strong> <?= htmlspecialchars($booking['request_status'] === 'rejected' ? 'Declined' : $booking['status']) ?></p>
					<p><strong>Price:</strong> <?= htmlspecialchars(number_format((float) $booking['price'], 2)) ?></p>
					<div class="booking-actions">
						<?php if (!empty($booking['service_id']) && in_array($booking['status'], ['confirmed', 'completed'], true)): ?>
							<form id="review-form-<?= (int) $booking['id'] ?>" class="review-form" action="/FixLine/cindex.php?action=review" method="post">
								<input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
								<input type="hidden" name="service_id" value="<?= (int) $booking['service_id'] ?>">
								<label>
									Rating (1-5)
									<input type="number" name="rating" min="1" max="5" required>
								</label>
								<label>
									Comment
									<textarea name="comment" rows="3"></textarea>
								</label>
							</form>
							<button type="submit" form="review-form-<?= (int) $booking['id'] ?>">Review</button>
							<form action="/FixLine/cindex.php?action=payment" method="post">
								<input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
								<button class="pay-button" type="submit">Pay</button>
							</form>
						<?php endif; ?>
						<?php if ($booking['status'] === 'pending'): ?>
							<form action="/FixLine/cindex.php?action=cancel_booking" method="post">
								<input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
								<button class="cancel-button" type="submit">Cancel</button>
							</form>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</body>
</html>
