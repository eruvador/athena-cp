<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('TransferTitle');

if (count($_POST)) {
	if ($session->account->balance) {
		$credits  = (int)$params->get('credits'); 
		$charName = trim($params->get('char_name'));
		
		if (!$credits || $credits < 1) {
			$errorMessage = Athena::message('TransferGreaterThanOne');
		}
		elseif (!$charName) {
			$errorMessage = Athena::message('TransferEnterCharName');
		}
		else {
			$res = $server->transferCredits($session->account->account_id, $charName, $credits);
			
			if ($res === -3) {
				$errorMessage = sprintf(Athena::message('TransferNoCharExists'), $charName);
			}
			elseif ($res === -2) {
				$errorMessage = Athena::message('TransferNoBalance');
			}
			elseif ($res !== true) {
				$errorMessage = Athena::message('TransferUnexpectedError');
			}
			else {
				$session->setMessageData(Athena::message('TransferSuccessful'));
				$this->redirect();
			}
		}
	}
	else {
		$this->deny();
	}
}
?>