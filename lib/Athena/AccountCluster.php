<?php
require_once 'Athena/ClusterError.php';

class Athena_AccountCluster extends Athena_LoginServer {
	/**
	 * Login server database.
	 *
	 * @access public
	 * @var string
	 */
	public $loginDatabase;
	
	public $connectTable;

	public function __construct(Athena_Config $config)
	{
		parent::__construct($config);
		$this->loginDatabase = $config->getDatabase();
		$this->connectTable = Athena::config('AthenaTables.ClusterTable');
	}
	
	/**
	 * Validate credentials against the login server's database information.
	 *
	 * @param string $username Ragnarok account username.
	 * @param string $password Ragnarok account password.
	 * @return bool True/false if valid or invalid.
	 * @access public
	 */
	public function isClusterAuth($username, $password)
	{
		if (Athena::config('PasswordEncodingSHA')) {
			$password = Athena::shaPassword($password);
		}
		else if (Athena::config('PasswordEncodingMD5')) {
			$password = Athena::hashPassword($password);
		}
		
		if (trim($username) == '' || trim($password) == '') {
			return false;
		}
		
		$sql  = "SELECT username FROM {$this->loginDatabase}.{$this->connectTable} ";
		$sql .= "WHERE LOWER(username) = LOWER(?) AND password = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		$sth->execute(array($username, $password));
		$res = $sth->fetch();
		
		return ($res) ? true : false;
	}

	/**
	 *
	 */
	public function create($username, $password, $confirmPassword, $email, $birthdate, $securityCode)
	{
		if (preg_match('/[^' . Athena::config('UsernameAllowedChars') . ']/', $username)) {
			throw new Athena_ClusterError('Invalid character(s) used in username', Athena_ClusterError::INVALID_USERNAME);
		}
		elseif (strlen($username) < Athena::config('MinUsernameLength')) {
			throw new Athena_ClusterError('Username is too short', Athena_ClusterError::USERNAME_TOO_SHORT);
		}
		elseif (strlen($username) > Athena::config('MaxUsernameLength')) {
			throw new Athena_ClusterError('Username is too long', Athena_ClusterError::USERNAME_TOO_LONG);
		}
		elseif (strlen($password) < Athena::config('MinPasswordLength')) {
			throw new Athena_ClusterError('Password is too short', Athena_ClusterError::PASSWORD_TOO_SHORT);
		}
		elseif (strlen($password) > Athena::config('MaxPasswordLength')) {
			throw new Athena_ClusterError('Password is too long', Athena_ClusterError::PASSWORD_TOO_LONG);
		}
		elseif ($password !== $confirmPassword) {
			throw new Athena_ClusterError('Passwords do not match', Athena_ClusterError::PASSWORD_MISMATCH);
		}
		elseif (!preg_match('/(.+?)@(.+?)/', $email)) {
			throw new Athena_ClusterError('Invalid e-mail address', Athena_ClusterError::INVALID_EMAIL_ADDRESS);
		}
		elseif (Athena::config('UseCaptcha')) {
			if (Athena::config('EnableReCaptcha')) {
				require_once 'recaptcha/recaptchalib.php';
				$resp = recaptcha_check_answer(
					Athena::config('ReCaptchaPrivateKey'),
					$_SERVER['REMOTE_ADDR'],
					$_POST['recaptcha_challenge_field'],
					$_POST['recaptcha_response_field']);
				
				if (!$resp->is_valid) {
					throw new Athena_ClusterError('Invalid security code', Athena_ClusterError::INVALID_SECURITY_CODE);
				}
			}
			elseif (strtolower($securityCode) !== strtolower(Athena::$sessionData->securityCode)) {
				throw new Athena_ClusterError('Invalid security code', Athena_ClusterError::INVALID_SECURITY_CODE);
			}
		}
		
		$sql  = "SELECT username FROM {$this->loginDatabase}.{$this->connectTable} WHERE LOWER(username) = LOWER(?) LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		$sth->execute(array($username));
		$res = $sth->fetch();
		if ($res) {
			throw new Athena_ClusterError('Username is already taken', Athena_ClusterError::USERNAME_ALREADY_TAKEN);
		}
		
		$sql = "SELECT email FROM {$this->loginDatabase}.{$this->connectTable} WHERE email = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		$sth->execute(array($email));
		$res = $sth->fetch();
		if ($res) {
			throw new Athena_ClusterError('E-mail address is already in use', Athena_ClusterError::EMAIL_ADDRESS_IN_USE);
		}
		
		if (Athena::config('PasswordEncodingSHA')) {
			$password = Athena::shaPassword($password);
		}
		else if (Athena::config('PasswordEncodingMD5')) {
			$password = Athena::hashPassword($password);
		}
		
		$col = "username, password, email, birthdate, reg_date, reg_ip";
		$val = array($username, $password, $email, $birthdate, 'NOW()', $_SERVER['REMOTE_ADDR']);
		if (Athena::config('RequireEmailConfirm')) {
			$col .= ", state, confirm_code";
			array_push($val, "1", $code = md5(rand()));
			
			if ($expire = Athena::config('EmailConfirmExpire')) {
				$sql .= ", confirm_expire = ?";
				$val[] = date('Y-m-d H:i:s', time() + (60 * 60 * $expire));
			}
		}
		$ins = preg_replace('((?:[a-z][a-z0-9_]*))', '?', $col);
		
		$sql = "INSERT INTO {$this->loginDatabase}.{$this->connectTable} ($col) VALUES ($ins)";
		$sth = $this->connection->getStatement($sql);
		$res = $sth->execute($val);
		
		if ($res) {		
			if (Athena::config('RequireEmailConfirm')) {
				require_once 'Athena/Mailer.php';
				
				$user = $username;
				$name = $session->loginAthenaGroup->serverName;
				$link = $this->url('connect', 'confirm', array('_host' => true, 'code' => $code, 'user' => $username, 'login' => $name));
				$mail = new Athena_Mailer();
				$sent = $mail->send($email, 'Account Confirmation', 'confirm', array('AccountUsername' => $username, 'ConfirmationLink' => htmlspecialchars($link)));
								
				if ($sent) {
					$message  = Athena::message('ClusterCreateEmailSent');
				}
				else {
					$message  = Athena::message('ClusterCreateFailed');
				}
				
				$session->setMessageData($message);
			}
			
			return true;
		}
		else {
			return false;
		}
	}
	
}
?>