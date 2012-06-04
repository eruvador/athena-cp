<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('PasswordChangeTitle');

if (count($_POST)) {
	$currentPassword    = $params->get('currentpass');
	$newPassword        = trim($params->get('newpass'));
	$confirmNewPassword = trim($params->get('confirmnewpass'));
	
	if (!$currentPassword) {
		$errorMessage = Athena::message('NeedCurrentPassword');
	}
	elseif (!$newPassword) {
		$errorMessage = Athena::message('NeedNewPassword');
	}
	elseif (strlen($newPassword) < Athena::config('MinPasswordLength')) {
		$errorMessage = Athena::message('PasswordTooShort');
	}
	elseif (strlen($newPassword) > Athena::config('MaxPasswordLength')) {
		$errorMessage = Athena::message('PasswordTooLong');
	}
	elseif (!$confirmNewPassword) {
		$errorMessage = Athena::message('ConfirmNewPassword');
	}
	elseif ($newPassword != $confirmNewPassword) {
		$errorMessage = Athena::message('PasswordsDoNotMatch');
	}
	elseif ($newPassword == $currentPassword) {
		$errorMessage = Athena::message('NewPasswordSameAsOld');
	}
	else {
		$sql = "SELECT user_pass AS currentPassword FROM {$server->loginDatabase}.login WHERE account_id = ?";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($session->account->account_id));
		
		$account         = $sth->fetch();
		$useMD5          = $session->loginServer->config->getUseMD5();
		$currentPassword = $useMD5 ? Athena::hashPassword($currentPassword) : $currentPassword;
		$newPassword     = $useMD5 ? Athena::hashPassword($newPassword) : $newPassword;
		
		if ($currentPassword != $account->currentPassword) {
			$errorMessage = Athena::message('OldPasswordInvalid');
		}
		else {
			$sql = "UPDATE {$server->loginDatabase}.login SET user_pass = ? WHERE account_id = ?";
			$sth = $server->connection->getStatement($sql);
			
			if ($sth->execute(array($newPassword, $session->account->account_id))) {
				$pwChangeTable = Athena::config('AthenaTables.ChangePasswordTable');
				
				$sql  = "INSERT INTO {$server->loginDatabase}.$pwChangeTable ";
				$sql .= "(account_id, old_password, new_password, change_ip, change_date) ";
				$sql .= "VALUES (?, ?, ?, ?, NOW())";
				$sth  = $server->connection->getStatement($sql);
				$sth->execute(array($session->account->account_id, $currentPassword, $newPassword, $_SERVER['REMOTE_ADDR']));
				
				$session->setMessageData(Athena::message('PasswordHasBeenChanged'));
				$session->logout();
				$this->redirect($this->url('account', 'login'));
			}
			else {
				$errorMessage = Athena::message('FailedToChangePassword');
			}
		}
	}
}
?>