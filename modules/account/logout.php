<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('LogoutTitle');

$session->logout();
$metaRefresh = array('seconds' => 2, 'location' => $this->basePath);
?>