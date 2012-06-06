<?php
require_once 'Athena/Installer/MainServer.php';

/**
 *
 */
class Athena_Installer {
	/**
	 *
	 */
	private static $installer;
	
	/**
	 *
	 */
	public $servers = array();
	
	/**
	 *
	 */
	private function __construct()
	{
		foreach (Athena::$loginAthenaGroupRegistry as $serverName => $loginAthenaGroup) {
			$this->servers[$serverName] = new Athena_Installer_MainServer($loginAthenaGroup);
		}
	}
	
	/**
	 *
	 */
	public static function getInstance()
	{
		if (!self::$installer) {
			self::$installer = new Athena_Installer();
		}
		return self::$installer;
	}
	
	/**
	 *
	 */
	public function updateNeeded()
	{
		foreach ($this->servers as $mainServer) {
			foreach ($mainServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					return true;
				}
			}
			foreach ($mainServer->charMapServers as $charMapServer) {
				foreach ($charMapServer->schemas as $schema) {
					if (!$schema->isLatest()) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *
	 */
	public function updateAll()
	{
		foreach ($this->servers as $mainServer) {
			foreach ($mainServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					$schema->update();
				}
			}
			foreach ($mainServer->charMapServers as $charMapServer) {
				foreach ($charMapServer->schemas as $schema) {
					if (!$schema->isLatest()) {
						$schema->update();
					}
				}
			}
		}
	}
}
?>