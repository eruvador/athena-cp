<?php
require_once 'Athena/Installer/Schema.php';
require_once 'Athena/Installer/CharMapServer.php';

/**
 *
 */
class Athena_Installer_MainServer {
	/**
	 *
	 */
	public $loginAthenaGroup;
	
	/**
	 *
	 */
	public $charMapServers = array();
	
	/**
	 *
	 */
	public $schemas;
	
	/**
	 *
	 */
	public function __construct(Athena_LoginAthenaGroup $loginAthenaGroup)
	{
		$this->loginAthenaGroup  = $loginAthenaGroup;
		$this->schemas           = Athena_Installer_Schema::getSchemas($this);
		
		if (array_key_exists($loginAthenaGroup->serverName, Athena::$athenaServerRegistry)) {
			foreach (Athena::$athenaServerRegistry[$loginAthenaGroup->serverName] as $athena) {
				$this->charMapServers[$athena->serverName] = new Athena_Installer_CharMapServer($this, $athena);
			}
		}
	}
	
	/**
	 *
	 */
	public function updateAll()
	{
		foreach ($this->schemas as $schema) {
			if (!$schema->isLatest()) {
				$schema->update();
			}
		}
		foreach ($this->charMapServers as $charMapServer) {
			foreach ($charMapServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					$schema->update();
				}
			}
		}
	}
}
?>