<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = 'Reset Look';

$charID = $params->get('id');
if (!$charID) {
	$this->deny();
}

$char = $server->getCharacter($charID);
if (!$char || ($char->account_id != $session->account->account_id && !$auth->allowedToResetLook)) {
	$this->deny();
}

$reset = $server->resetLook($charID);
if ($reset === -1) {
	$message = sprintf(Athena::message('CantResetLookWhenOnline'), $char->name);
}
elseif ($reset === true) {
	$message = sprintf(Athena::message('ResetLookSuccessful'), $char->name);
}
else {
	$message = sprintf(Athena::message('ResetLookFailed'), $char->name);
}

$session->setMessageData($message);
$this->redirect($this->url('character', 'view', array('id' => $charID)));
?>