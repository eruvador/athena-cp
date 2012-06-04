<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

if (!$auth->allowedToDeleteShopItem) {
	$this->deny();
}

require_once 'Athena/ItemShop.php';

$shop       = new Athena_ItemShop($server);
$shopItemID = $params->get('id');
$deleted    = $shopItemID ? $shop->delete($shopItemID) : false;

if ($deleted) {
	$session->setMessageData('Item successfully deleted from the item shop.');
	$this->redirect($this->url('purchase'));
}
?>