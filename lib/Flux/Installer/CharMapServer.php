<?php
require_once 'Athena/Installer/Schema.php';

/**
 *
 */
class Athena_Installer_CharMapServer {
	/**
	 *
	 */
	public $mainServer;
	
	/**
	 *
	 */
	public $athena;
	
	/**
	 *
	 */
	public $schemas;
	
	/**
	 *
	 */
	public function __construct(Athena_Installer_MainServer $mainServer, Athena_Athena $athena)
	{
		$this->mainServer = $mainServer;
		$this->athena     = $athena;
		$this->schemas    = Athena_Installer_Schema::getSchemas($mainServer, $this);
	}
}
?>