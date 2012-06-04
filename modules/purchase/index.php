<?php
if (!defined('ATHENA_ROOT')) exit;

$title = 'Item Shop';

require_once 'Athena/ItemShop.php';

$category      = $params->get('category');
$categories    = Athena::config("ShopCategories")->toArray();
$categoryName  = Athena::config("ShopCategories.$category");
$categoryCount = array();
$shop          = new Athena_ItemShop($server);
$items         = $shop->getItems($category);
$sql           = sprintf("SELECT COUNT(id) AS total FROM %s.%s WHERE category = ?", $server->charMapDatabase, Athena::config('AthenaTables.ItemShopTable'));
$sql2          = sprintf("SELECT COUNT(id) AS total FROM %s.%s", $server->charMapDatabase, Athena::config('AthenaTables.ItemShopTable'));
$sth           = $server->connection->getStatement($sql);
$sth2          = $server->connection->getStatement($sql2);
$sth2->execute();
$total         = $sth2->fetch()->total;

foreach ($categories as $catID => $catName) {
	$sth->execute(array($catID));
	$categoryCount[$catID] = $sth->fetch()->total;
}
?>