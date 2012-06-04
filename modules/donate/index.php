<?php 
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired(Athena::message('LoginToDonate'));

$title = 'Make a Donation';

$donationAmount = false;

if (count($_POST) && $params->get('setamount')) {
	$minimum = Athena::config('MinDonationAmount');
	$amount  = (float)$params->get('amount');
	
	if (!$amount || $amount < $minimum) {
		$errorMessage = sprintf('Donation amount must be greater than or equal to %s %s!',
			$this->formatCurrency($minimum), Athena::config('DonationCurrency'));
	}
	else {
		$donationAmount = $amount;
	}
}

if (!$params->get('setamount') && $params->get('resetamount')) {
	$this->redirect($this->url);
}
?>