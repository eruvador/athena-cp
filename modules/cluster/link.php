<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('ClusterLinkTitle');

if (Athena::config('UseCaptcha') && Athena::config('EnableReCaptcha')) {
	require_once 'recaptcha/recaptchalib.php';
	$recaptcha = recaptcha_get_html(Athena::config('ReCaptchaPublicKey'));
}

if (count($_POST)) {
	require_once 'Athena/AccountCluster.php';
	require_once 'Athena/ClusterError.php';
	
	try {
		$username   = $params->get('username');
		$password   = $params->get('password');
		$confirm    = $params->get('confirm_password');
		$code       = $params->get('security_code');
		
		$result     = $server->loginServer->link($session->account->cluster_id, $username, $password, $confirm, $code);

		if ($result) {
			if (Athena::config('RequireEmailConfirm')) {
				require_once 'Athena/Mailer.php';
				
				$user = $username;
				$name = $session->loginAthenaGroup->serverName;
				$link = $this->url('cluster', 'linkconfirm', array('_host' => true, 'code' => $code, 'user' => $username, 'login' => $name));
				$mail = new Athena_Mailer();
				$sent = $mail->send($email, 'Account Link Confirmation', 'confirm', array('AccountUsername' => $username, 'ConfirmationLink' => htmlspecialchars($link)));
								
				if ($sent) {
					$message  = Athena::message('ClusterLinkEmailSent');
				}
				else {
					$message  = Athena::message('ClusterLinkFailed');
				}
				
				$session->setMessageData($message);
			}
			else {
				$session->setMessageData(Athena::message('ClusterLinkCreated'));
			}
			$this->redirect($this->url('cluster', 'link'));
		}
		else {
			exit('Uh oh, what happened?');
		}
	}
	catch (Athena_ClusterError $e) {
		switch ($e->getCode()) {
			case Athena_ClusterError::UNEXPECTED:
				$errorMessage = Athena::message('UnexpectedLoginError');
				break;
			case Athena_ClusterError::INVALID_LINK:
				$errorMessage = Athena::message('InvalidLinkCredentials');
				break;
			case Athena_ClusterError::INVALID_SECURITY_CODE:
				$errorMessage = Athena::message('InvalidSecurityCode');
				break;
			default:
				$errorMessage = Athena::message('CriticalLoginError');
				break;
		}
	}
}
?>