<?php
/**
 * The BaseServer implementation is used for containing methods and properties
 * directly related to the server itself, such as checking its upstatus.
 */
class Athena_BaseServer {
	/**
	 * The configuration object for this server. For a login server this would
	 * be the Athena_Config instance of the LoginServer section, for a character
	 * server it would be CharServer and so on.
	 *
	 * @access public
	 * @var Athena_Config
	 */
	public $config;
	
	/**
	 * Construct new server object. Should be overridden by child class.
	 *
	 * @access public
	 */
	public function __construct(Athena_Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Checks whether the server is up and running (accepting connections).
	 * Will return true/false based on the server status.
	 *
	 * @return bool Returns true if server is running, false if not.
	 * @access public
	 */
	public function isUp()
	{
		$addr = $this->config->getAddress();
		$port = $this->config->getPort();
		$sock = @fsockopen($addr, $port, $errno, $errstr, (int)Athena::config('ServerStatusTimeout'));
		
		if (is_resource($sock)) {
			fclose($sock);
			return true;
		}
		else {
			return false;
		}
	}
}
?>