<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('ClusterViewTitle');

$clusterID = $params->get('id');
if (!$clusterID) {
	$clusterID  = $session->account->cluster_id;
}

$clusterTable  = Athena::config('AthenaTables.ClusterTable');
$accLinksTable = Athena::config('AthenaTables.ClusterLinksTable');

$sql  = "SELECT cluster.* FROM {$server->loginDatabase}.{$clusterTable} AS cluster WHERE cluster.cluster_id = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($clusterID));
$cluster = $sth->fetch();

if($cluster) {
	$sql  = "SELECT login.account_id, login.userid, login.sex, login.lastlogin, links.confirmed FROM {$server->loginDatabase}.login ";
	$sql .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$accLinksTable} AS links ON login.account_id = links.account_id ";
	$sql .= "WHERE links.cluster_id = ?";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($clusterID));
	$links = $sth->fetchAll();
}
?>