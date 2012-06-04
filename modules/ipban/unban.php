<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

if (!count($_POST) || !$params->get('unban') ) {
	$this->deny();
}

if (!(($unbanList=$params->get('unban_list')) instanceOf Athena_Config) || !count($unbanList=$unbanList->toArray())) {
	$session->setMessageData(Athena::message('IpbanNothingToUnban'));
}
else {
	$reason = trim($params->get('reason'));
	
	if (!$reason) {
		$session->setMessageData(Athena::message('IpbanEnterUnbanReason'));
	}
	else {
		$didAllSucceed = true;
		$numFailed = 0;
		
		foreach ($unbanList as $unban) {
			if (!$server->loginServer->removeIpBan($session->account->account_id, $reason, $unban)) {
				$didAllSucceed = false;
				$numFailed++;
			}
		}
		
		if ($didAllSucceed) {
			$session->setMessageData(Athena::message('IpbanUnbanned'));
		}
		else {
			$session->setMessageData(sprintf(Athena::message('IpbanUnbanFailed'), $numFailed));
		}
	}
}

$this->redirect($this->url('ipban'));
?>