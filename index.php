<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$action = $_GET['action'] ?? 'login';

if ($action === 'dashboard' && isset($_SESSION['user_role'])) {
	$action = match ($_SESSION['user_role']) {
		'admin' => 'admin_dashboard',
		'provider' => 'provider_dashboard',
		'moderator' => 'moderator_dashboard',
		'finance' => 'finance_dashboard',
		default => 'customer_dashboard',
	};
}

if ($action === 'logout') {
	$_SESSION = [];
	session_destroy();
	header('Location: /FixLine/index.php?action=login');
	exit;
}

function requireRole(array $roles): void
{
	if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles, true)) {
		header('Location: /FixLine/index.php?action=login');
		exit;
	}
}

$authActions = ['login', 'customer_signup', 'provider_signup'];
$customerActions = ['customer_dashboard', 'dashboard', 'search', 'request_service', 'post_job', 'my_bookings', 'book', 'review', 'cancel_booking', 'payment', 'refunds', 'account_settings'];
$adminActions = ['admin_dashboard', 'settings', 'update_settings_email', 'update_settings_pass', 'account_management', 'profile_detail', 'save_profile', 'toggle_block', 'update_provider_status', 'analytics', 'users_info'];

if (in_array($action, $authActions, true)) {
	require_once __DIR__ . '/Controller/AuthController.php';
	$authController = new AuthController();
	$authMethod = $action === 'login' ? 'login' : ($action === 'provider_signup' ? 'registerProvider' : 'registerCustomer');
	$authController->{$authMethod}();
	exit;
}

if (in_array($action, $adminActions, true)) {
	requireRole(['admin']);
	if ($action === 'admin_dashboard') {
		$_GET['action'] = 'dashboard';
	}
	require_once __DIR__ . '/Controller/AdministratorController.php';
	(new AdministratorController())->handleRequest();
	exit;
}

if (in_array($action, $customerActions, true)) {
	requireRole(['customer']);
	require_once __DIR__ . '/Controller/CustomerController.php';
	$customerAction = match ($action) {
		'customer_dashboard', 'dashboard' => 'dashboard',
		'request_service' => 'requestService',
		'post_job' => 'postJob',
		'my_bookings' => 'myBookings',
		'cancel_booking' => 'cancelBooking',
		'account_settings' => 'accountSettings',
		default => $action,
	};
	(new CustomerController())->{$customerAction}();
	exit;
}

if ($action === 'provider_dashboard' || in_array($action, ['provider_profile', 'provider_requests', 'provider_apply_job', 'provider_earnings', 'upload_picture'], true)) {
	requireRole(['provider']);
	$_GET['action'] = match ($action) {
		'provider_dashboard' => 'dashboard',
		'provider_profile' => 'profile',
		'provider_requests' => 'requests',
		'provider_apply_job' => 'apply_job',
		'provider_earnings' => 'earnings',
		default => $action,
	};
	require_once __DIR__ . '/Controller/ServiceProviderController.php';
	(new ServiceProviderController())->handleRequest();
	exit;
}

if (in_array($action, ['moderator_dashboard', 'moderator_customers', 'moderator_providers', 'moderator_complaints', 'moderator_account_management'], true)) {
	requireRole(['moderator']);
	require_once __DIR__ . '/Controller/ModeratorController.php';
	$moderatorController = new ModeratorController();
	$moderatorMethod = match ($action) {
		'moderator_customers' => 'customers',
		'moderator_providers' => 'providers',
		'moderator_complaints' => 'complaints',
		'moderator_account_management' => 'accountManagement',
		default => 'index',
	};
	$moderatorController->{$moderatorMethod}();
	exit;
}

if (in_array($action, ['finance_dashboard', 'finance_payments', 'finance_payout', 'finance_refunds', 'finance_account_management'], true)) {
	requireRole(['finance']);
	require_once __DIR__ . '/Controller/FinanceOfficerController.php';
	$financeController = new FinanceOfficerController();
	$financeMethod = match ($action) {
		'finance_dashboard' => 'dashboard',
		'finance_payments' => 'payments',
		'finance_payout' => 'payout',
		'finance_refunds' => 'refunds',
		default => 'accountManagement',
	};
	$financeController->{$financeMethod}();
	exit;
}

http_response_code(404);
echo 'Page not found';