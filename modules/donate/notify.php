<?php
if (!defined('ATHENA_ROOT')) exit;

require_once 'Athena/PaymentNotifyRequest.php';
if (count($_POST)) {
	$request = new Athena_PaymentNotifyRequest($_POST);
	$request->process();
}
exit;
?>