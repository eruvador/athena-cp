<?php
require_once 'Athena/Config.php';

class Athena_Addon {
	public $name;
	public $addonDir;
	public $configDir;
	public $moduleDir;
	public $themeDir;
	public $addonConfig;
	public $accessConfig;
	public $messagesConfig;
	
	public function __construct($name, $addonDir = null)
	{
		$this->name       = $name;
		$this->addonDir   = is_null($addonDir) ? ATHENA_ADDON_DIR."/$name" : $addonDir;
		$this->configDir  = "{$this->addonDir}/config";
		$this->moduleDir  = "{$this->addonDir}/modules";
		$this->themeDir   = "{$this->addonDir}/themes/".Athena::config('ThemeName');
		
		$files = array(
			'addonConfig'    => "{$this->configDir}/addon.php",
			'accessConfig'   => "{$this->configDir}/access.php",
			//'messagesConfig' => "{$this->configDir}/messages.php" // Deprecated.
		);
		
		foreach ($files as $configName => $filename) {
			if (file_exists($filename)) {
				$this->{$configName} = Athena::parseConfigFile($filename);
			}
			
			if (!($this->{$configName} instanceOf Athena_Config)) {
				$tempArr = array();
				$this->{$configName} = new Athena_Config($tempArr);
			}
		}
		
		// Use new language system for messages (also supports addons).
		$this->messagesConfig = Athena::parseLanguageConfigFile($name);
	}
	
	public function respondsTo($module, $action = null)
	{
		$path = is_null($action) ? "{$this->moduleDir}/$module" : "{$this->moduleDir}/$module/$action.php";
		if ((is_null($action) && is_dir($path)) || file_exists($path)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function hasView($module, $action)
	{
		$path = "{$this->themeDir}/$module/$action.php";
		if (file_exists($path)) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>