<?php
if (!defined('ATHENA_ROOT')) exit;

if (!extension_loaded('zip')) {
	throw new Athena_Error('The `zip` extension needs to be loaded for this feature to work.  Please consult the PHP manual for instructions.');
}

$this->loginRequired();

$title = 'Export Guild Emblems';

require_once 'Athena/EmblemExporter.php';
$exporter = new Athena_EmblemExporter($session->loginAthenaGroup);

$serverNames = $session->getAthenaServerNames();

if (count($_POST)) {
	$serverArr = $params->get('server');
	
	if ($serverArr instanceOf Athena_Config) {
		$array = $serverArr->toArray();
		
		foreach ($array as $serv) {
			$athenaServer = $session->getAthenaServer($serv);
			
			if ($athenaServer) {
				$exporter->addAthenaServer($athenaServer);
			}
		}
		
		$exporter->exportArchive();
	}
	else {
		$session->setMessageData('You must select a server first.');
	}
}
?>