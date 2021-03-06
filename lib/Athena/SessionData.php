<?php
require_once 'Athena/DataObject.php';
require_once 'Athena/ItemShop/Cart.php';
require_once 'Athena/LoginError.php';

/**
 * Contains all of Athena's session data.
 */
class Athena_SessionData {
	/**
	 * Actual session data array.
	 *
	 * @access private
	 * @var array
	 */
	private $sessionData;
	
	/**
	 * Session data filters.
	 *
	 * @access private
	 * @var array
	 */
	private $dataFilters = array();
	
	/**
	 * Selected login server group.
	 *
	 * @access public
	 * @var Athena_LoginAthenaGroup
	 */
	public $loginAthenaGroup;
	
	/**
	 * Selected login server.
	 *
	 * @access public
	 * @var Athena_LoginServer
	 */
	public $loginServer;
	
	/**
	 * Account object.
	 *
	 * @access public
	 * @var Athena_DataObject
	 */
	public $account;

	
	/**
	 * Create new SessionData instance.
	 *
	 * @param array $sessionData
	 * @access public
	 */
	public function __construct(array &$sessionData, $logout = false)
	{
		$this->sessionData = &$sessionData;
		if ($logout) {
			$this->logout();
		}
		else {
			$this->initialize();
		}
	}
	
	/**
	 * Initialize session data.
	 *
	 * @param bool $force
	 * @return bool
	 * @access private
	 */
	private function initialize($force = false)
	{	
		$keysToInit = array('username', 'serverName', 'athenaServerName', 'securityCode');
		foreach ($keysToInit as $key) {
			if ($force || !$this->{$key}) {
				$method = ucfirst($key);
				$method = "set{$method}Data";
				$this->$method(null);
			}
		}

		$loggedIn = true;
		if (!$this->username) {
			$loggedIn = false;
			$cfgAthenaServerName = Athena::config('DefaultCharMapServer');
			$cfgLoginAthenaGroup = Athena::config('DefaultLoginGroup');
			
			if (Athena::getServerGroupByName($cfgLoginAthenaGroup)){
				$this->setServerNameData($cfgLoginAthenaGroup);
			}
			else {
				$defaultServerName = current(array_keys(Athena::$loginAthenaGroupRegistry));
				$this->setServerNameData($defaultServerName);
			}
		}
		
		
		if ($this->serverName && ($this->loginAthenaGroup = Athena::getServerGroupByName($this->serverName))) {
			$this->loginServer = $this->loginAthenaGroup->loginServer;
			
			if (!$loggedIn && $cfgAthenaServerName && $this->getAthenaServer($cfgAthenaServerName)) {
				$this->setAthenaServerNameData($cfgAthenaServerName);
			}
			
			if (!$this->athenaServerName || ((!$loggedIn && !$this->getAthenaServer($cfgAthenaServerName)) || !$this->getAthenaServer($this->athenaServerName))) {
				$this->setAthenaServerNameData(current($this->getAthenaServerNames()));
			}
		}
		
		// Get new account data every request.
		if ($this->loginAthenaGroup && $this->username && ($account = $this->getAccount($this->loginAthenaGroup, $this->username))) {
			$this->account = $account;
			
			// Automatically log out of account when detected as banned.
			$permBan = ($account->state == 5 && !Athena::config('AllowPermBanLogin'));
			$tempBan = (($account->unban_time > 0 && $account->unban_time < time()) && !Athena::config('AllowTempBanLogin'));
			
			if ($permBan || $tempBan) {
				$this->logout();
			}
		}
		else {
			$this->account = new Athena_DataObject(null, array('group_id' => AccountLevel::UNAUTH));
		}
		
		//if (!$this->isLoggedIn()) {
		//	$this->setServerNameData(null);
		//	$this->setAthenaServerNameData(null);
		//}
		
		if (!is_array($this->cart)) {
			$this->setCartData(array());
		}
		
		if ($this->account->account_id && $this->loginAthenaGroup) {
			if (!array_key_exists($this->loginAthenaGroup->serverName, $this->cart)) {
				$this->cart[$this->loginAthenaGroup->serverName] = array();
			}

			foreach ($this->getAthenaServerNames() as $athenaServerName) {
				$athenaServer = $this->getAthenaServer($athenaServerName);
				$cartArray    = &$this->cart[$this->loginAthenaGroup->serverName];
				$accountID    = $this->account->account_id;
				
				if (!array_key_exists($accountID, $cartArray)) {
					$cartArray[$accountID] = array();
				}
				
				if (!array_key_exists($athenaServerName, $cartArray[$accountID])) {
					$cartArray[$accountID][$athenaServerName] = new Athena_ItemShop_Cart();
				}
				$cartArray[$accountID][$athenaServerName]->setAccount($this->account);
				$athenaServer->setCart($cartArray[$accountID][$athenaServerName]);
			}
		}
		
		return true;
	}
	
	/**
	 * Log current user out.
	 * 
	 * @return bool
	 * @access public
	 */
	public function logout()
	{
		$this->loginAthenaGroup = null;
		$this->loginServer = null;
		return $this->initialize(true);
	}
	
	public function __call($method, $args)
	{
		if (count($args) && preg_match('/set(.+?)Data/', $method, $m)) {
			$arg     = current($args);
			$meth    = $m[1];
			$meth[0] = strtolower($meth[0]);
			
			if (array_key_exists($meth, $this->dataFilters)) {
				foreach ($this->dataFilters[$meth] as $callback) {
					$arg = call_user_func($callback, $arg);
				}
			}
			
			$this->sessionData[$meth] = $arg;
		}
	}
	
	public function &__get($prop)
	{
		$value = null;
		if (array_key_exists($prop, $this->sessionData)) {
			$value = &$this->sessionData[$prop];
		}
		return $value;
	}
	
	/**
	 * Set session data.
	 *
	 * @param array $keys Session keys to be affected.
	 * @param mixed $value Value to be assigned to all specified keys.
	 * @return mixed whatever was set
	 * @access public
	 */
	public function setData(array $keys, $value)
	{
		foreach ($keys as $key) {
			$key = ucfirst($key);
			$this->{"set{$key}Data"}($value);
		}
		return $value;
	}
	
	/**
	 * Add a session data setter filter.
	 *
	 * @param string $key Which session key
	 * @param string $callback Function callback.
	 * @return string Callback
	 * @access public
	 */
	public function addDataFilter($key, $callback)
	{
		if (!array_key_exists($key, $this->dataFilters)) {
			$this->dataFilters[$key] = array();
		}
		
		$this->dataFilters[$key][] = $callback;
		return $callback;
	}
	
	/**
	 * Checks whether the current user is logged in.
	 */
	public function isLoggedIn()
	{
		return $this->account->group_id >= AccountLevel::NORMAL;
	}
	
	/**
	 * User login.
	 *
	 * @param string $server Server name
	 * @param string $username
	 * @param string $password
	 * @throws Athena_LoginError
	 * @access public
	 */
	public function login($server, $username, $password, $securityCode = null)
	{
		$loginAthenaGroup = Athena::getServerGroupByName($server);
		if (!$loginAthenaGroup) {
			throw new Athena_LoginError('Invalid server.', Athena_LoginError::INVALID_SERVER);
		}
		
		if ($loginAthenaGroup->loginServer->isIpBanned() && !Athena::config('AllowIpBanLogin')) {
			throw new Athena_LoginError('IP address is banned', Athena_LoginError::IPBANNED);
		}
		
		if ($securityCode !== false && Athena::config('UseLoginCaptcha')) {
			if (strtolower($securityCode) != strtolower($this->securityCode)) {
				throw new Athena_LoginError('Invalid security code', Athena_LoginError::INVALID_SECURITY_CODE);
			}
			elseif (Athena::config('EnableReCaptcha')) {
				require_once 'recaptcha/recaptchalib.php';
				$resp = recaptcha_check_answer(
					Athena::config('ReCaptchaPrivateKey'),
					$_SERVER['REMOTE_ADDR'],
					// Checks POST fields.
					$_POST['recaptcha_challenge_field'],
					$_POST['recaptcha_response_field']);
				
				if (!$resp->is_valid) {
					throw new Athena_LoginError('Invalid security code', Athena_LoginError::INVALID_SECURITY_CODE);
				}
			}
		}
		
		if (!$loginAthenaGroup->isAuth($username, $password)) {
			throw new Athena_LoginError('Invalid login', Athena_LoginError::INVALID_LOGIN);
		}
		
		$creditsTable  = Athena::config('AthenaTables.CreditsTable');
		$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';
		
		$sql  = "SELECT login.*, {$creditColumns} FROM {$loginAthenaGroup->loginDatabase}.login ";
		$sql .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
		$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.userid = ? LIMIT 1";
		$smt  = $loginAthenaGroup->connection->getStatement($sql);
		$res  = $smt->execute(array($username));
		
		if ($res && ($row = $smt->fetch())) {
			if ($row->unban_time > 0) {
				if (time() >= $row->unban_time) {
					$row->unban_time = 0;
					$sql = "UPDATE {$loginAthenaGroup->loginDatabase}.login SET unban_time = 0 WHERE account_id = ?";
					$sth = $loginAthenaGroup->connection->getStatement($sql);
					$sth->execute(array($row->account_id));
				}
				elseif (!Athena::config('AllowTempBanLogin')) {
					throw new Athena_LoginError('Temporarily banned', Athena_LoginError::BANNED);
				}
			}
			if ($row->state == 5) {
				$createTable = Athena::config('AthenaTables.AccountCreateTable');
				$sql  = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$createTable ";
				$sql .= "WHERE account_id = ? AND confirmed = 0";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$sth->execute(array($row->account_id));
				$row2 = $sth->fetch();
				
				if ($row2 && $row2->id) {
					throw new Athena_LoginError('Pending confirmation', Athena_LoginError::PENDING_CONFIRMATION);
				}
			}
			if (!Athena::config('AllowPermBanLogin') && $row->state == 5) {
				throw new Athena_LoginError('Permanently banned', Athena_LoginError::PERMABANNED);
			}
			
			$this->setServerNameData($server);
			$this->setUsernameData($username);
			$this->initialize(false);
		}
		else {
			$message  = "Unexpected error during login.\n";
			$message .= 'PDO error info, if any: '.print_r($smt->errorInfo(), true);
			throw new Athena_LoginError($message, Athena_LoginError::UNEXPECTED);
		}
		
		return true;
	}
	
	/**
	 * Get account object for a particular user name.
	 *
	 * @param Athena_LoginAthenaGroup $loginAthenaGroup
	 * @param string $username
	 * @return mixed
	 * @access private
	 */
	private function getAccount(Athena_LoginAthenaGroup $loginAthenaGroup, $username)
	{
		$creditsTable  = Athena::config('AthenaTables.CreditsTable');
		$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';
		
		$sql  = "SELECT login.*, {$creditColumns} FROM {$loginAthenaGroup->loginDatabase}.login ";
		$sql .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
		$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.userid = ? LIMIT 1";
		$smt  = $loginAthenaGroup->connection->getStatement($sql);
		$res  = $smt->execute(array($username));
		
		if ($res && ($row = $smt->fetch())) {
			return $row;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get available server names.
	 *
	 * @access public
	 */
	public function getAthenaServerNames()
	{
		if ($this->loginAthenaGroup) {
			$names = array();
			foreach ($this->loginAthenaGroup->athenaServers as $server) {
				$names[] = $server->serverName;
			}
			return $names;
		}
		else {
			return array();
		}
	}
	
	/**
	 * Get a Athena_Athena instance by its name based on current server settings.
	 * 
	 * @param string $name
	 * @access public
	 */
	public function getAthenaServer($name = null)
	{
		if (is_null($name) && $this->athenaServerName) {
			return $this->getAthenaServer($this->athenaServerName);
		}
		
		if ($this->loginAthenaGroup && ($server = Athena::getAthenaServerByName($this->serverName, $name))) {
			return $server;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get flash message.
	 *
	 * @return string
	 * @access public
	 */
	public function getMessage()
	{
		$message = $this->message;
		$this->setMessageData(null);
		return $message;
	}
}
?>