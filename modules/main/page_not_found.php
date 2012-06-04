<?php
if (!defined('ATHENA_ROOT')) exit;

header('HTTP/1.1 404 Not Found');
$title = Athena::message('PageNotFoundTitle');
?>