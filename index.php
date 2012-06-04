<?php
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
	echo '<h2>Error</h2>';
	echo '<p>PHP 5.2.1 or higher is required to use Athena Control Panel.</p>';
	echo '<p>You are running '.PHP_VERSION.'</p>';
	exit;
}

// Disable Zend Engine 1 compatibility mode.
// See: http://www.php.net/manual/en/ini.core.php#ini.zend.ze1-compatibility-mode
ini_set('zend.ze1_compatibility_mode', 0);

// Time started.
define('__START__', microtime(true));

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ATHENA_ROOT',       str_replace('\\', '/', dirname(__FILE__)));
define('ATHENA_DATA_DIR',   'data');
define('ATHENA_CONFIG_DIR', 'config');
define('ATHENA_LIB_DIR',    'lib');
define('ATHENA_MODULE_DIR', 'modules');
define('ATHENA_THEME_DIR',  'themes');
define('ATHENA_ADDON_DIR',  'addons');
define('ATHENA_LANG_DIR',   'lang');

// Clean GPC arrays in the event magic_quotes_gpc is enabled.
if (ini_get('magic_quotes_gpc')) {
	$gpc = array(&$_GET, &$_POST, &$_REQUEST, &$_COOKIE);
	foreach ($gpc as &$arr) {
		foreach ($arr as $key => $value) {
			if (is_string($value)) {
				$arr[$key] = stripslashes($value);
			}
		}
	}
}

set_include_path(ATHENA_LIB_DIR.PATH_SEPARATOR.get_include_path());

// Default account levels.
require_once ATHENA_CONFIG_DIR.'/levels.php';

// Some necessary Athena core libraries.
require_once 'Athena.php';
require_once 'Athena/Dispatcher.php';
require_once 'Athena/SessionData.php';
require_once 'Athena/DataObject.php';
require_once 'Athena/Authorization.php';
require_once 'Athena/Installer.php';
require_once 'Athena/PermissionError.php';

// Vendor libraries.
require_once 'markdown/markdown.php';

try {
	if (!extension_loaded('pdo')) {
		throw new Athena_Error('The PDO extension is required to use Athena, please make sure it is installed along with the PDO_MYSQL driver.');
	}
	elseif (!extension_loaded('pdo_mysql')) {
		throw new Athena_Error('The PDO_MYSQL driver for the PDO extension must be installed to use Athena.  Please consult the PHP manual for installation instructions.');
	}

	// Initialize Athena.
	Athena::initialize(array(
		'appConfigFile'      => ATHENA_CONFIG_DIR.'/application.php',
		'serversConfigFile'  => ATHENA_CONFIG_DIR.'/servers.php',
		//'messagesConfigFile' => ATHENA_CONFIG_DIR.'/messages.php' // No longer needed (Deprecated)
	));

	// Set time limit.
	set_time_limit((int)Athena::config('ScriptTimeLimit'));

	// Set default timezone for entire app.
	$timezone = Athena::config('DateDefaultTimezone');
	if ($timezone && !@date_default_timezone_set($timezone)) {
		throw new Athena_Error("'$timezone' is not a valid timezone.  Consult http://php.net/timezones for a list of valid timezones.");
	}

	// Create some basic directories.
	$directories = array(
		ATHENA_DATA_DIR.'/logs/schemas',
		ATHENA_DATA_DIR.'/logs/schemas/logindb',
		ATHENA_DATA_DIR.'/logs/schemas/charmapdb',
		ATHENA_DATA_DIR.'/logs/transactions',
		ATHENA_DATA_DIR.'/logs/mail',
		ATHENA_DATA_DIR.'/logs/mysql',
		ATHENA_DATA_DIR.'/logs/mysql/errors',
		ATHENA_DATA_DIR.'/logs/errors',
		ATHENA_DATA_DIR.'/logs/errors/exceptions',
		ATHENA_DATA_DIR.'/logs/errors/mail',
	);

	// Schema log directories.
	foreach (Athena::$loginAthenaGroupRegistry as $serverName => $loginAthenaGroup) {
		$directories[] = ATHENA_DATA_DIR."/logs/schemas/logindb/$serverName";
		$directories[] = ATHENA_DATA_DIR."/logs/schemas/charmapdb/$serverName";

		foreach ($loginAthenaGroup->athenaServers as $athenaServer)
			$directories[] = ATHENA_DATA_DIR."/logs/schemas/charmapdb/$serverName/{$athenaServer->serverName}";
	}

	foreach ($directories as $directory) {
		if (is_writable(dirname($directory)) && !is_dir($directory)) {
			if (Athena::config('RequireOwnership'))
				mkdir($directory, 0700);
			else
				mkdir($directory, 0777);
		}
	}
	
	if (Athena::config('RequireOwnership') && function_exists('posix_getuid'))
		$uid = posix_getuid();
	
	$directories = array(
		ATHENA_DATA_DIR.'/logs'     => 'log storage',
		ATHENA_DATA_DIR.'/itemshop' => 'item shop image',
		ATHENA_DATA_DIR.'/tmp'      => 'temporary'
	);
	
	foreach ($directories as $directory => $directoryFunction) {
		$directory = realpath($directory);
		if (!is_writable($directory))
			throw new Athena_PermissionError("The $directoryFunction directory '$directory' is not writable.  Remedy with `chmod 0600 $directory`");
		if (Athena::config('RequireOwnership') && function_exists('posix_getuid') && fileowner($directory) != $uid)
			throw new Athena_PermissionError("The $directoryFunction directory '$directory' is not owned by the executing user.  Remedy with `chown -R ".posix_geteuid().":".posix_geteuid()." $directory`");
	}
	
	if (ini_get('session.use_trans_sid'))
		throw new Athena_Error("The 'session.use_trans_sid' php.ini configuration must be turned off for Athena to work.");

	// Installer library.
	$installer = Athena_Installer::getInstance();
	if ($hasUpdates=$installer->updateNeeded())
		Athena::config('ThemeName', 'installer');

	$sessionKey = Athena::config('SessionKey');
	$sessionExpireDuration = Athena::config('SessionCookieExpire') * 60 * 60;
	session_set_cookie_params($sessionExpireDuration, Athena::config('BaseURI'));
	ini_set('session.gc_maxlifetime', $sessionExpireDuration);
	ini_set('session.name', $sessionKey);
	@session_start();

	if (empty($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
		$_SESSION[$sessionKey] = array();
	}

	// Initialize session data.
	Athena::$sessionData = new Athena_SessionData($_SESSION[$sessionKey], $hasUpdates);

	// Initialize authorization component.
	$accessConfig = Athena::parseConfigFile(ATHENA_CONFIG_DIR.'/access.php');

	// Merge with add-on configs.
	foreach (Athena::$addons as $addon) {
		$accessConfig->merge($addon->accessConfig);
	}

	$accessConfig->set('unauthorized.index', AccountLevel::ANYONE);
	$authComponent = Athena_Authorization::getInstance($accessConfig, Athena::$sessionData);

	if (!Athena::config('DebugMode')) {
		ini_set('display_errors', 0);
	}

	// Dispatch requests->modules->actions->views.
	$dispatcher = Athena_Dispatcher::getInstance();
	$dispatcher->setDefaultModule(Athena::config('DefaultModule'));
	$dispatcher->dispatch(array(
		'basePath'                  => Athena::config('BaseURI'),
		'useCleanUrls'              => Athena::config('UseCleanUrls'),
		'modulePath'                => ATHENA_MODULE_DIR,
		'themePath'                 => ATHENA_THEME_DIR.'/'.Athena::config('ThemeName'),
		'missingActionModuleAction' => Athena::config('DebugMode') ? array('errors', 'missing_action') : array('main', 'page_not_found'),
		'missingViewModuleAction'   => Athena::config('DebugMode') ? array('errors', 'missing_view')   : array('main', 'page_not_found')
	));
}
catch (Exception $e) {
	$exceptionDir = ATHENA_DATA_DIR.'/logs/errors/exceptions';
	if (is_writable($exceptionDir)) {
		require_once 'Athena/LogFile.php';
		$today = date('Ymd');
		$eLog  = new Athena_LogFile("$exceptionDir/$today.log");

		// Log exception.
		$eLog->puts('(%s) Exception %s: %s', get_class($e), get_class($e), $e->getMessage());
		foreach (explode("\n", $e->getTraceAsString()) as $traceLine) {
			$eLog->puts('(%s) **TRACE** %s', get_class($e), $traceLine);
		}
	}

	require_once ATHENA_CONFIG_DIR.'/error.php';
	define('__ERROR__', 1);
	include $errorFile;
}
?>