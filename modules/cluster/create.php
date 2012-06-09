<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('ClusterCreateTitle');

if (Athena::config('UseCaptcha') && Athena::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Athena::config('ReCaptchaPublicKey'));
}

if (count($_POST)) {
	require_once 'Athena/AccountCluster.php';
	require_once 'Athena/ClusterError.php';
	
	try {		
		$username  = $params->get('username');
		$password  = $params->get('password');
		$confirm   = $params->get('confirm_password');
		$email     = $params->get('email_address');
		$code      = $params->get('security_code');
		
		$result    = $server->loginServer->create($username, $password, $confirm, $email, $code);

		if ($result) {
			if (!Athena::config('RequireEmailConfirm')) {
				$session->login($server->serverName, $username, $password, false);
				$session->setMessageData(Athena::message('ClusterCreated'));
			}
			$this->redirect();
		}
		else {
			exit('Uh oh, what happened?');
		}
	}
	catch (Athena_ClusterError $e) {
		switch ($e->getCode()) {
			case Athena_ClusterError::USERNAME_ALREADY_TAKEN:
				$errorMessage = Athena::message('UsernameAlreadyTaken');
				break;
			case Athena_ClusterError::USERNAME_TOO_SHORT:
				$errorMessage = Athena::message('UsernameTooShort');
				break;
			case Athena_ClusterError::USERNAME_TOO_LONG:
				$errorMessage = Athena::message('UsernameTooLong');
				break;
			case Athena_ClusterError::PASSWORD_TOO_SHORT:
				$errorMessage = Athena::message('PasswordTooShort');
				break;
			case Athena_ClusterError::PASSWORD_TOO_LONG:
				$errorMessage = Athena::message('PasswordTooLong');
				break;
			case Athena_ClusterError::PASSWORD_MISMATCH:
				$errorMessage = Athena::message('PasswordsDoNotMatch');
				break;
			case Athena_ClusterError::EMAIL_ADDRESS_IN_USE:
				$errorMessage = Athena::message('EmailAddressInUse');
				break;
			case Athena_ClusterError::INVALID_EMAIL_ADDRESS:
				$errorMessage = Athena::message('InvalidEmailAddress');
				break;
			case Athena_ClusterError::INVALID_SECURITY_CODE:
				$errorMessage = Athena::message('InvalidSecurityCode');
				break;
			case Athena_ClusterError::INVALID_USERNAME:
				$errorMessage = sprintf(Athena::message('AccountInvalidChars'), Athena::config('UsernameAllowedChars'));
				break;
			default:
				$errorMessage = Athena::message('CriticalRegisterError');
				break;
		}
	}
}
?>