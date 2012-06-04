<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('MailerTitle');
$preview = '';

if (count($_POST)) {
	$prev    = (bool)$params->get('_preview');
	$to      = trim($params->get('to'));
	$subject = trim($params->get('subject'));
	$body    = trim($params->get('body'));
	
	if (!$to) {
		$errorMessage = Athena::message('MailerEnterToAddress');
	}
	elseif (!$subject) {
		$errorMessage = Athena::message('MailerEnterSubject');
	}
	elseif (!$body) {
		$errorMessage = Athena::message('MailerEnterBodyText');
	}
	
	if (empty($errorMessage)) {
		if ($prev) {
			require_once 'markdown/markdown.php';
			$preview = Markdown($body);
		}
		else {
			require_once 'Athena/Mailer.php';
			
			$mail = new Athena_Mailer();
			$opts = array('_ignoreTemplate' => true, '_useMarkdown' => true);
			
			if ($mail->send($to, $subject, $body, $opts)) {
				$session->setMessageData(sprintf(Athena::message('MailerEmailHasBeenSent'), $to));
				$this->redirect();
			}
			else {
				$errorMessage = Athena::message('MailerFailedToSend');
			}
		}
	}
}
?>