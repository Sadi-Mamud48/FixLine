<?php

class FinanceOfficer
{
	private $name;

	public function __construct($name = 'Finance Officer')
	{
		$this->name = $name;
	}

	public function getDashboardTitle()
	{
		return $this->name . ' Dashboard';
	}

	public function getNavigationItems()
	{
		return [
			['label' => 'Payments', 'url' => 'payments.php', 'image' => 'payment-method (1).png'],
			['label' => 'Payout', 'url' => 'payout.php', 'image' => 'atm.png'],
			['label' => 'Refunds', 'url' => 'refunds.php', 'image' => 'refund.png'],
			['label' => 'Account Management', 'url' => 'account-management.php', 'image' => 'accountant.png'],
		];
	}

	public function getAccountDetails()
	{
		return [];
	}
}
