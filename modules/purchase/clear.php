<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

require_once 'Athena/ItemShop.php';

if ($server->cart && $server->cart->clear()) {
	$session->setMessageData("Your cart has been emptied.");
}
else {
	$session->setMessageData("Couldn't empty your cart, maybe it's already empty.");
}

$this->redirect($this->url('purchase'));
?>