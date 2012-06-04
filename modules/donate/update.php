<?php 
if (!defined('ATHENA_ROOT')) exit;


if (!($password=$params->get('password')) || $password !== Athena::config('InstallerPassword')) {
	$this->deny();
}
else {
	Athena::processHeldCredits();
	exit('DONE');
}
?>