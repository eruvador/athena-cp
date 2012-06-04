<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('ResetPassTitle');

$serverNames    = $this->getServerNames();
$resetPassTable = Athena::config('AthenaTables.ResetPasswordTable');

if (count($_POST)) {
	$userid    = $params->get('userid');
	$email     = $params->get('email');
	$groupName = $params->get('login');
	
	if (!$userid) {
		$errorMessage = Athena::message('ResetPassEnterAccount');
	}
	elseif (!$email) {
		$errorMessage = Athena::message('ResetPassEnterEmail');
	}
	else {
		if (!$groupName || !($loginAthenaGroup=Athena::getServerGroupByName($groupName))) {
			$loginAthenaGroup = $session->loginAthenaGroup;
		}

		$sql  = "SELECT account_id, user_pass, group_id FROM {$loginAthenaGroup->loginDatabase}.login WHERE ";
		if ($loginAthenaGroup->loginServer->config->getNoCase()) {
			$sql .= 'LOWER(userid) = LOWER(?) ';
		}
		else {
			$sql .= 'BINARY userid = ? ';
		}
		$sql .= "AND email = ? AND state = 0 AND sex IN ('M', 'F') LIMIT 1";
		$sth  = $loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($userid, $email));

		$row = $sth->fetch();
		if ($row) {
			if ($row->group_id >= Athena::config('NoResetPassLevel')) {
				$errorMessage = Athena::message('ResetPassDisallowed');
			}
			else {
				$code = md5(rand() + $row->account_id);
				$sql  = "INSERT INTO {$loginAthenaGroup->loginDatabase}.$resetPassTable ";
				$sql .= "(code, account_id, old_password, request_date, request_ip, reset_done) ";
				$sql .= "VALUES (?, ?, ?, NOW(), ?, 0)";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$res  = $sth->execute(array($code, $row->account_id, $row->user_pass, $_SERVER['REMOTE_ADDR']));
				
				if ($res) {
					require_once 'Athena/Mailer.php';
					$name = $loginAthenaGroup->serverName;
					$link = $this->url('account', 'resetpw', array('_host' => true, 'code' => $code, 'account' => $row->account_id, 'login' => $name));
					$mail = new Athena_Mailer();
					$sent = $mail->send($email, 'Reset Password', 'resetpass', array('AccountUsername' => $userid, 'ResetLink' => htmlspecialchars($link)));
				}
			}
		}

		if (empty($errorMessage)) {
			if (empty($sent)) {
				$errorMessage = Athena::message('ResetPassFailed');
			}
			else {
				$session->setMessageData(Athena::message('ResetPassEmailSent'));
				$this->redirect();
			}
		}
	}
}
?>