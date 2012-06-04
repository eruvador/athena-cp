<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('MissingViewTitle');
$realViewPath = sprintf('%s/%s/%s/%s.php', ATHENA_ROOT, $this->themePath, $this->params->get('module'), $this->params->get('action'));
?>