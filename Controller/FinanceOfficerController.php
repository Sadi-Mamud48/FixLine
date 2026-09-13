<?php
require_once __DIR__ . '/../Model/FinanceOfficer.php';
require_once __DIR__ . '/../Model/Payment.php';

class FinanceOfficerController {
    private $financeModel;
    private $paymentModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->financeModel = new FinanceOfficer();
        $this->paymentModel = new Payment();
    }

    public function dashboard() {
        $dashboardTitle = $this->financeModel->getDashboardTitle();
        $navigationItems = $this->financeModel->getNavigationItems();
        require_once __DIR__ . '/../View/FinanceOfficer/financeofficerdashboard.php';
    }

    public function payments() {
        $payments = $this->paymentModel->getAllTransactions();
        require_once __DIR__ . '/../View/FinanceOfficer/payments.php';
    }

    public function payout() {
        $payouts = $this->paymentModel->getPendingPayouts();
        require_once __DIR__ . '/../View/FinanceOfficer/payout.php';
    }

    public function refunds() {
        $refunds = $this->paymentModel->getRefundRequests();
        require_once __DIR__ . '/../View/FinanceOfficer/refunds.php';
    }

    public function accountManagement() {
        $accountSummary = $this->financeModel->getAccountDetails();
        require_once __DIR__ . '/../View/FinanceOfficer/account-management.php';
    }
}