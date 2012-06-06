<?php
if (!defined('ATHENA_ROOT')) exit;

$athenaVersion  = Athena::VERSION;
$athenaVersion .= Athena::GITHASH ? '.'.Athena::GITHASH : '';
?>