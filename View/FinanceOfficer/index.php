<?php

require_once __DIR__ . '/../../Controller/FinanceOfficerController.php';

$action = $_GET['action'] ?? 'dashboard';
$controller = new FinanceOfficerController();

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'payments':
        $controller->payments();
        break;
    case 'payout':
        $controller->payout();
        break;
    case 'refunds':
        $controller->refunds();
        break;
    case 'account_management':
        $controller->accountManagement();
        break;
    default:
        http_response_code(404);
        echo 'Finance Officer page not found.';
}