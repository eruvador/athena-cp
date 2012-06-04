<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('MissingActionTitle');
$realActionPath = sprintf('%s/%s/%s/%s.php', ATHENA_ROOT, $this->modulePath, $this->params->get('module'), $this->params->get('action'));
?>