<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

if ($server->cart->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('purchase'));
}

$title = 'Shopping Cart';

require_once 'Athena/ItemShop.php';
$items = $server->cart->getCartItems();
?>