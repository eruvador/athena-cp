<?php
require_once 'Athena/Error.php';

/**
 * The authorization component allows you to find out whether or not the
 * the current user is allowed to perform a certain task based on his account
 * level.
 */
class Athena_Authorization {
	/**
	 * Authorization instance.
	 *
	 * @access private
	 * @var Athena_Authorization
	 */
	private static $auth;
	
	/**
	 * Access configuration.
	 *
	 * @access private
	 * @var Athena_Config
	 */
	private $config;
	
	/**
	 * Session data object.
	 *
	 * @access private
	 * @var Athena_SessionData
	 */
	private $session;
	
	/**
	 * Construct new Athena_Authorization instance.
	 *
	 * @param Athena_Config $accessConfig
	 * @param Athena_SessionData $sessionData
	 * @access private
	 */
	private function __construct(Athena_Config $accessConfig, Athena_SessionData $sessionData)
	{
		$this->config  = $accessConfig;
		$this->session = $sessionData;
	}
	
	/**
	 * Get authorization instance, creates one if it doesn't already exist.
	 *
	 * @param Athena_Config $accessConfig
	 * @param Athena_SessionData $sessionData
	 * @return Athena_Authorization
	 * @access public
	 */
	public static function getInstance($accessConfig = null, $sessionData = null)
	{
		if (!self::$auth) {
			self::$auth = new Athena_Authorization($accessConfig, $sessionData);
		}
		return self::$auth;	
	}
	
	/**
	 * Checks whether or not the current user is able to perform a particular
	 * action based on his/her level.
	 *
	 * @param string $moduleName
	 * @param string $actionName
	 * @return bool
	 * @access public
	 */
	public function actionAllowed($moduleName, $actionName = 'index')
	{
		$accessConfig = $this->config->get('modules');
		$accessKeys   = array("$moduleName.$actionName", "$moduleName.*");
		$accountLevel = $this->session->account->group_id;
		$existentKeys = array();
		
		if ($accessConfig instanceOf Athena_Config) {
			foreach ($accessKeys as $accessKey) {
				$accessLevel = $accessConfig->get($accessKey);
			
				if (!is_null($accessLevel)) {
					$existentKeys[] = $accessKey;
					
					if (($accessLevel == AccountLevel::ANYONE || $accessLevel == $accountLevel ||
						($accessLevel != AccountLevel::UNAUTH && $accessLevel <= $accountLevel))) {
					
						return true;
					}
				}
			}
		}
		
		if (empty($existentKeys)) {
			return -1;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Checks whether or not the current user is allowed to use a particular
	 * feature based on his/her level.
	 *
	 * @param string $featureName
	 * @return bool
	 * @access public
	 */
	public function featureAllowed($featureName)
	{
		$accessConfig = $this->config->get('features');
		$accountLevel = $this->session->account->group_id;
		
		if (($accessConfig instanceOf Athena_Config)) {
			$accessLevel = $accessConfig->get($featureName);
			
			if (!is_null($accessLevel) &&
				($accessLevel == AccountLevel::ANYONE || $accessLevel == $accountLevel ||
				($accessLevel != AccountLevel::UNAUTH && $accessLevel <= $accountLevel))) {
			
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Provides convenient getters such as `allowedTo<FeatureName>' and
	 * `getLevelTo<FeatureName>'.
	 *
	 * @access public
	 */
	public function __get($prop)
	{
		if (preg_match("/^allowedTo(.+)/i", $prop, $m)) {
			return $this->featureAllowed($m[1]);
		}
		elseif (preg_match("/^getLevelTo(.+)/i", $prop, $m)) {
			$accessConfig = $this->config->get('features');
			if ($accessConfig instanceOf Athena_Config) {
				return $accessConfig->get($m[1]);
			}
		}
	}
	
	/**
	 * Wrapper method for setting and getting values from the access config.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param arary $options
	 * @access public
	 */
	public function config($key, $value = null, $options = array())
	{
		if (!is_null($value)) {
			return $this->config->set($key, $value, $options);
		}
		else {
			return $this->config->get($key);
		}
	}
}
?>