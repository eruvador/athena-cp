<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = 'Reset Position';

$charID = $params->get('id');
if (!$charID) {
	$this->deny();
}

$char = $server->getCharacter($charID);
if (!$char || ($char->account_id != $session->account->account_id && !$auth->allowedToResetPosition)) {
	$this->deny();
}

$reset = $server->resetPosition($charID);
if ($reset === -1) {
	$message = sprintf(Athena::message('CantResetPosWhenOnline'), $char->name);
}
elseif ($reset === -2) {
	$message = sprintf(Athena::message('CantResetFromCurrentMap'), $char->name);
}
elseif ($reset === true) {
	$message = sprintf(Athena::message('ResetPositionSuccessful'), $char->name);
}
else {
	$message = sprintf(Athena::message('ResetPositionFailed'), $char->name);
}

$session->setMessageData($message);
$this->redirect($this->url('character', 'view', array('id' => $charID)));
?>