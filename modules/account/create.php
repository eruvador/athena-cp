<?php
if (!defined('ATHENA_ROOT')) exit;

if (Athena::config('UseCaptcha') && Athena::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Athena::config('ReCaptchaPublicKey'));
}

$title = Athena::message('AccountCreateTitle');

$serverNames = $this->getServerNames();

if (count($_POST)) {
	require_once 'Athena/RegisterError.php';
	
	try {
		$server    = $params->get('server');
		$username  = $params->get('username');
		$password  = $params->get('password');
		$confirm   = $params->get('confirm_password');
		$email     = $params->get('email_address');
		$gender    = $params->get('gender');
		$code      = $params->get('security_code');
		$birthdate = $params->get('birth_date');
		
		if (!($server = Athena::getServerGroupByName($server))) {
			throw new Athena_RegisterError('Invalid server', Athena_RegisterError::INVALID_SERVER);
		}
		
		// Woohoo! Register ;)
		$result = $server->loginServer->register($username, $password, $confirm, $email, $gender, $code, $birthdate);

		if ($result) {
			if (Athena::config('RequireEmailConfirm')) {
				require_once 'Athena/Mailer.php';
				
				$user = $username;
				$code = md5(rand());
				$name = $session->loginAthenaGroup->serverName;
				$link = $this->url('account', 'confirm', array('_host' => true, 'code' => $code, 'user' => $username, 'login' => $name));
				$mail = new Athena_Mailer();
				$sent = $mail->send($email, 'Account Confirmation', 'confirm', array('AccountUsername' => $username, 'ConfirmationLink' => htmlspecialchars($link)));
				
				$createTable = Athena::config('AthenaTables.AccountCreateTable');
				$bind = array($code);
				
				// Insert confirmation code.
				$sql  = "UPDATE {$server->loginDatabase}.{$createTable} SET ";
				$sql .= "confirm_code = ?, confirmed = 0 ";
				if ($expire=Athena::config('EmailConfirmExpire')) {
					$sql .= ", confirm_expire = ? ";
					$bind[] = date('Y-m-d H:i:s', time() + (60 * 60 * $expire));
				}
				
				$sql .= " WHERE account_id = ?";
				$bind[] = $result;
				
				$sth  = $server->connection->getStatement($sql);
				$sth->execute($bind);
				
				$session->loginServer->permanentlyBan(null, sprintf(Athena::message('AccountConfirmBan'), $code), $result);
				
				if ($sent) {
					$message  = Athena::message('AccountCreateEmailSent');
				}
				else {
					$message  = Athena::message('AccountCreateFailed');
				}
				
				$session->setMessageData($message);
				$this->redirect();
			}
			else {
				$session->login($server->serverName, $username, $password, false);
				$session->setMessageData(Athena::message('AccountCreated'));
				$this->redirect();
			}
		}
		else {
			exit('Uh oh, what happened?');
		}
	}
	catch (Athena_RegisterError $e) {
		switch ($e->getCode()) {
			case Athena_RegisterError::USERNAME_ALREADY_TAKEN:
				$errorMessage = Athena::message('UsernameAlreadyTaken');
				break;
			case Athena_RegisterError::USERNAME_TOO_SHORT:
				$errorMessage = Athena::message('UsernameTooShort');
				break;
			case Athena_RegisterError::USERNAME_TOO_LONG:
				$errorMessage = Athena::message('UsernameTooLong');
				break;
			case Athena_RegisterError::PASSWORD_TOO_SHORT:
				$errorMessage = Athena::message('PasswordTooShort');
				break;
			case Athena_RegisterError::PASSWORD_TOO_LONG:
				$errorMessage = Athena::message('PasswordTooLong');
				break;
			case Athena_RegisterError::PASSWORD_MISMATCH:
				$errorMessage = Athena::message('PasswordsDoNotMatch');
				break;
			case Athena_RegisterError::EMAIL_ADDRESS_IN_USE:
				$errorMessage = Athena::message('EmailAddressInUse');
				break;
			case Athena_RegisterError::INVALID_EMAIL_ADDRESS:
				$errorMessage = Athena::message('InvalidEmailAddress');
				break;
			case Athena_RegisterError::INVALID_GENDER:
				$errorMessage = Athena::message('InvalidGender');
				break;
			case Athena_RegisterError::INVALID_SERVER:
				$errorMessage = Athena::message('InvalidServer');
				break;
			case Athena_RegisterError::INVALID_SECURITY_CODE:
				$errorMessage = Athena::message('InvalidSecurityCode');
				break;
			case Athena_RegisterError::INVALID_BIRTHDATE_FORMAT:
				$errorMessage = Athena::message('BirthDateError');
				break;
			case Athena_RegisterError::BIRTHDATE_MUSTNOTBE_EMPTY:
				$errorMessage = Athena::message('BirthDateEmptyError');
				break;
			case Athena_RegisterError::INVALID_USERNAME:
				$errorMessage = sprintf(Athena::message('AccountInvalidChars'), Athena::config('UsernameAllowedChars'));
				break;
			default:
				$errorMessage = Athena::message('CriticalRegisterError');
				break;
		}
	}
}
?>