<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('LoginTitle');

if (Athena::config('UseLoginCaptcha') && Athena::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Athena::config('ReCaptchaPublicKey'));
}

$loginLogTable = Athena::config('AthenaTables.LoginLogTable');

if (count($_POST)) {
	$server   = $params->get('server');
	$username = $params->get('username');
	$password = $params->get('password');
	$code     = $params->get('security_code');
	
	try {
		$session->login($server, $username, $password, $code);
		$returnURL = $params->get('return_url');
		
		if (Athena::config('PasswordEncodingSHA')) {
			$password = Athena::shaPassword($password);
		}
		else if (Athena::config('PasswordEncodingMD5')) {
			$password = Athena::hashPassword($password);
		}
		
		$sql  = "INSERT INTO {$session->loginAthenaGroup->loginDatabase}.$loginLogTable ";
		$sql .= "(account_id, username, password, ip, error_code, login_date) ";
		$sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
		$sth  = $session->loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($session->account->account_id, $username, $password, $_SERVER['REMOTE_ADDR'], null));
		
		if ($returnURL) {
			$this->redirect($returnURL);
		}
		else {
			$this->redirect();
		}
	}
	catch (Athena_LoginError $e) {
		if ($username && $password && $e->getCode() != Athena_LoginError::INVALID_SERVER) {
			$loginAthenaGroup = Athena::getServerGroupByName($server);

			$sql = "SELECT account_id FROM {$loginAthenaGroup->loginDatabase}.login WHERE ";
			
			if (!$loginAthenaGroup->loginServer->config->getNoCase()) {
				$sql .= "CAST(userid AS BINARY) ";
			} else {
				$sql .= "userid ";
			}
			
			$sql .= "= ? LIMIT 1";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute(array($username));
			$row = $sth->fetch();

			if ($row) {
				$accountID = $row->account_id;
				
				if ($loginAthenaGroup->loginServer->config->getUseMD5()) {
					$password = Athena::hashPassword($password);
				}

				$sql  = "INSERT INTO {$loginAthenaGroup->loginDatabase}.$loginLogTable ";
				$sql .= "(account_id, username, password, ip, error_code, login_date) ";
				$sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$sth->execute(array($accountID, $username, $password, $_SERVER['REMOTE_ADDR'], $e->getCode()));
			}
		}
		
		switch ($e->getCode()) {
			case Athena_LoginError::UNEXPECTED:
				$errorMessage = Athena::message('UnexpectedLoginError');
				break;
			case Athena_LoginError::INVALID_SERVER:
				$errorMessage = Athena::message('InvalidLoginServer');
				break;
			case Athena_LoginError::INVALID_LOGIN:
				$errorMessage = Athena::message('InvalidLoginCredentials');
				break;
			case Athena_LoginError::BANNED:
				$errorMessage = Athena::message('TemporarilyBanned');
				break;
			case Athena_LoginError::PERMABANNED:
				$errorMessage = Athena::message('PermanentlyBanned');
				break;
			case Athena_LoginError::IPBANNED:
				$errorMessage = Athena::message('IpBanned');
				break;
			case Athena_LoginError::INVALID_SECURITY_CODE:
				$errorMessage = Athena::message('InvalidSecurityCode');
				break;
			case Athena_LoginError::PENDING_CONFIRMATION:
				$errorMessage = Athena::message('PendingConfirmation');
				break;
			default:
				$errorMessage = Athena::message('CriticalLoginError');
				break;
		}
	}
}

$serverNames = $this->getServerNames();
?>