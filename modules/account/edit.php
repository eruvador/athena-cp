<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('AccountEditTitle');

$accountID = $params->get('id');

$creditsTable  = Athena::config('AthenaTables.CreditsTable');
$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';


$sql  = "SELECT login.*, {$creditColumns} FROM {$server->loginDatabase}.login ";
$sql .= "LEFT OUTER JOIN {$creditsTable} AS credits ON login.account_id = credits.account_id ";
$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.account_id = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($accountID));

// Account object.
$account = $sth->fetch();
$isMine  = false;

if ($account) {
	if ($account->group_id > $session->account->group_id && !$auth->allowedToEditHigherPower) {
		$this->deny();
	}
	
	$isMine = $account->account_id == $session->account->account_id;
	
	if ($isMine) {
		$title = Athena::message('AccountEditTitle2');
	}
	else {
		$title = sprintf(Athena::message('AccountEditTitle3'), $account->userid);
	}
	
	if (count($_POST)) {
		$email      = trim($params->get('email'));
		$gender     = trim($params->get('gender'));
		$loginCount = (int)$params->get('logincount');
		$lastLogin  = $params->get('lastlogin_date');
		$lastIP     = trim($params->get('last_ip'));
		$group_id   = (int)$params->get('group_id');
		$balance    = (int)$params->get('balance');
		
		if ($isMine && $account->group_id != $group_id) {
			$errorMessage = Athena::message('CannotModifyOwnLevel');
		}
		elseif ($account->group_id != $group_id && !$auth->allowedToEditAccountLevel) {
			$errorMessage = Athena::message('CannotModifyAnyLevel');
		}
		elseif ($group_id > $session->account->group_id) {
			$errorMessage = Athena::message('CannotModifyLevelSoHigh');
		}
		elseif (!in_array($gender, array('M', 'F'))) {
			$errorMessage = Athena::message('InvalidGender');
		}
		elseif ($account->balance != $balance && !$auth->allowedToEditAccountBalance) {
			$errorMessage = Athena::message('CannotModifyBalance');
		}
		elseif ($lastLogin && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $lastLogin)) {
			$errorMessage = Athena::message('InvalidLastLoginDate');
		}
		else {
			$bind = array(
				'email'      => $email,
				'sex'        => $gender,
				'logincount' => $loginCount,
				'lastlogin'  => $lastLogin ? $lastLogin : $account->lastlogin,
				'last_ip'    => $lastIP
			);
			
			$sql  = "UPDATE {$server->loginDatabase}.login SET email = :email, ";
			$sql .= "sex = :sex, logincount = :logincount, lastlogin = :lastlogin, last_ip = :last_ip";
			
			if ($auth->allowedToEditAccountLevel) {
				$sql .= ", group_id = :group_id";
				$bind['group_id'] = $group_id;
			}
			
			$bind['account_id'] = $account->account_id;
			
			$sql .= " WHERE account_id = :account_id";
			$sth  = $server->connection->getStatement($sql);
			$sth->execute($bind);

			if ($auth->allowedToEditAccountBalance) {
				$deposit = $balance - $account->balance;
				$session->loginServer->depositCredits($account->account_id, $deposit);
			}
			
			$session->setMessageData(Athena::message('AccountModified'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
	}
}
?>