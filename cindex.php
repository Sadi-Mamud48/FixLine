<?php

session_start();

$action = $_GET['action'] ?? 'dashboard';

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: /FixLine/View/Auth/login.php');
    exit();
}

require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/CustomerController.php';

$authController = new AuthController();
$customerController = new CustomerController();

switch ($action) {
    case 'login':
        $authController->login();
        break;

    case 'customer_signup':
        $authController->registerCustomer();
        break;

    case 'provider_signup':
        $authController->registerProvider();
        break;

    case 'dashboard':
        $customerController->dashboard();
        break;

    case 'search':
        $customerController->search();
        break;

    case 'request_service':
        $customerController->requestService();
        break;

    case 'my_bookings':
        $customerController->myBookings();
        break;

    case 'book':
        $customerController->book();
        break;

    case 'review':
        $customerController->review();
        break;

    case 'cancel_booking':
        $customerController->cancelBooking();
        break;

    case 'payment':
        $customerController->payment();
        break;

    case 'refunds':
        $customerController->refunds();
        break;

    case 'account_settings':
        $customerController->accountSettings();
        break;

    default:
        http_response_code(404);
        echo 'Page not found';
        break;
}
