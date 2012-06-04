<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('IpbanRemoveTitle');

$list = $params->get('list');

if (!$auth->allowedToRemoveIpBan || !$list) {
	$this->deny();
}

$sql  = "SELECT list FROM {$server->loginDatabase}.ipbanlist ";
$sql .= "WHERE rtime > NOW() AND list = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($list));

$ipban = $sth->fetch();

if (count($_POST)) {
	if (!$params->get('remipban')) {
		$this->deny();
	}
	
	$reason = trim($params->get('reason'));
	
	if (!$list) {
		$errorMessage = Athena::message('IpbanEnterIpPattern');
	}
	elseif (!preg_match('/^([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)$/', $list, $m)) {
		$errorMessage = Athena::message('IpbanInvalidPattern');
	}
	elseif (!$reason) {
		$errorMessage = Athena::message('IpbanEnterRemoveReason');
	}
	elseif (!$ipban || !$ipban->list) {
		$errorMessage = sprintf(Athena::message('IpbanNotBanned'), $list);
	}
	elseif ($server->loginServer->removeIpBan($session->account->account_id, $reason, $list)) {
		$session->setMessageData(sprintf(Athena::message('IpbanPatternUnbanned'), $list));
		$this->redirect($this->url('ipban'));
	}
	else {
		$errorMessage = Athena::message('IpbanRemoveFailed');
	}
}
?>