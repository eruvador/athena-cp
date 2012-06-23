<?php
require_once 'Athena/DataObject.php';
require_once 'Athena/AccountCluster.php';
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
		$keysToInit = array('username', 'clusterID', 'serverName', 'athenaServerName', 'securityCode');
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
		if ($this->loginAthenaGroup && $this->username && ($account = $this->getCluster($this->loginAthenaGroup, $this->clusterID))) {
			$this->account = $account;
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

		$clusterTable = Athena::config('AthenaTables.ClusterTable');
		
		if (!$loginAthenaGroup->isClusterAuth($username, $password)) {
			throw new Athena_LoginError('Invalid login', Athena_LoginError::INVALID_LOGIN);
		}
		
		$sql  = "SELECT cluster_id AS clusterID, state FROM {$loginAthenaGroup->loginDatabase}.{$clusterTable} ";
		$sql .= "WHERE username = ? LIMIT 1";
		$smt  = $loginAthenaGroup->connection->getStatement($sql);
		$res  = $smt->execute(array($username));
		
		if ($res && ($row = $smt->fetch())) {
			if ($row->state == 1) {
				throw new Athena_LoginError('Pending confirmation', Athena_LoginError::PENDING_CONFIRMATION);
			}

			$this->setServerNameData($server);
			$this->setUsernameData($username);
			$this->setClusterIDData($row->clusterID);
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
	private function getCluster(Athena_LoginAthenaGroup $loginAthenaGroup, $cluster_id)
	{
		$clusterTable  = Athena::config('AthenaTables.ClusterTable');
		$accLinksTable = Athena::config('AthenaTables.ClusterLinksTable');
		
		$sql   = "SELECT cluster.*, MAX(login.group_id) AS group_id ";
		$sql  .= "FROM {$loginAthenaGroup->loginDatabase}.{$clusterTable} AS cluster ";
		$sql  .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$accLinksTable} AS links ON cluster.cluster_id = links.cluster_id ";
		$sql  .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.login ON links.account_id = login.account_id ";
		$sql  .= "WHERE cluster.cluster_id = ? LIMIT 1";
		$smt   = $loginAthenaGroup->connection->getStatement($sql);
		$res   = $smt->execute(array($cluster_id));
		
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