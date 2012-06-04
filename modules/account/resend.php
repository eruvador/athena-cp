<?php
if (!defined('ATHENA_ROOT')) exit;

//if (!Athena::config('RequireEmailConfirm')) {
//	$this->deny();
//}

$title = Athena::message('ResendTitle');

$serverNames = $this->getServerNames();
$createTable = Athena::config('AthenaTables.AccountCreateTable');

if (count($_POST)) {
	$userid    = $params->get('userid');
	$email     = $params->get('email');
	$groupName = $params->get('login');
	
	if (!$userid) {
		$errorMessage = Athena::message('ResendEnterUsername');
	}
	elseif (!$email) {
		$errorMessage = Athena::message('ResendEnterEmail');
	}
	else {
		if (!$groupName || !($loginAthenaGroup=Athena::getServerGroupByName($groupName))) {
			$loginAthenaGroup = $session->loginAthenaGroup;
		}

		$sql  = "SELECT confirm_code FROM {$loginAthenaGroup->loginDatabase}.$createTable WHERE ";
		$sql .= "userid = ? AND email = ? AND confirmed = 0 AND confirm_expire > NOW() LIMIT 1";
		$sth  = $loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($userid, $email));

		$row  = $sth->fetch();
		if ($row) {
			require_once 'Athena/Mailer.php';
			$code = $row->confirm_code;
			$name = $loginAthenaGroup->serverName;
			$link = $this->url('account', 'confirm', array('_host' => true, 'code' => $code, 'user' => $userid, 'login' => $name));
			$mail = new Athena_Mailer();
			$sent = $mail->send($email, 'Account Confirmation', 'confirm', array('AccountUsername' => $userid, 'ConfirmationLink' => htmlspecialchars($link)));
		}

		if (empty($sent)) {
			$errorMessage = Athena::message('ResendFailed');
		}
		else {
			$session->setMessageData(Athena::message('ResendEmailSent'));
			$this->redirect();
		}
	}
}
?>