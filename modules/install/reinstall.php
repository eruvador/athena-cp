<?php
if (!defined('ATHENA_ROOT')) exit;

$title = 'Re-Install Database Schemas';

if (count($_POST) && $params->get('reinstall')) {
	$loginDbFiles   = glob(ATHENA_DATA_DIR.'/logs/schemas/logindb/*/*.txt');
	$charMapDbFiles = glob(ATHENA_DATA_DIR.'/logs/schemas/charmapdb/*/*.txt');
	
	foreach (array($loginDbFiles, $charMapDbFiles) as $dbDir) {
		if ($dbDir) {
			foreach ($dbDir as $file) {
				unlink($file);
			}
			// Attempt to unlink the directory, but let's not display an error if
			// there are still files in it.
			@rmdir($dbDir);
		}
	}
	
	$this->redirect();
}
?>