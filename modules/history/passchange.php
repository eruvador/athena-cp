<?php
if (!defined('ATHENA_ROOT')) exit;

$this->loginRequired();

$title = Athena::message('HistoryPassChangeTitle');
$passwordChangeTable = Athena::config('AthenaTables.ChangePasswordTable');

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$passwordChangeTable WHERE account_id = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('change_date', 'change_ip'));

$sql = "SELECT change_date, change_ip FROM {$server->loginDatabase}.$passwordChangeTable WHERE account_id = ?";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$changes = $sth->fetchAll();
?>