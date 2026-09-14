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
			['label' => 'Payments', 'url' => '/FixLine/index.php?action=finance_payments', 'image' => 'payment-method (1).png'],
			['label' => 'Payout', 'url' => '/FixLine/index.php?action=finance_payout', 'image' => 'atm.png'],
			['label' => 'Refunds', 'url' => '/FixLine/index.php?action=finance_refunds', 'image' => 'refund.png'],
			['label' => 'Account Management', 'url' => '/FixLine/index.php?action=finance_account_management', 'image' => 'accountant.png'],
		];
	}

	public function getAccountDetails()
	{
		return [];
	}
}
