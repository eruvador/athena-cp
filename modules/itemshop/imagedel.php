<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$shopItemID = $params->get('id');

if (!$shopItemID) {
	$this->deny();
}

require_once 'Athena/ItemShop.php';

$shop = new Athena_ItemShop($server);
$shop->deleteShopItemImage($shopItemID);

$session->setMessageData('Shop item image has been deleted.');
$this->redirect($this->referer);
?>