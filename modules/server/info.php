<?php
if (!defined('ATHENA_ROOT')) exit;

$title = Athena::message('ServerInfoTitle');
$info  = array(
		'accounts'   => 0,
		'characters' => 0,
		'guilds'     => 0,
		'parties'    => 0,
		'zeny'       => 0,
		'classes'    => array()
);

// Accounts.
$sql = "SELECT COUNT(account_id) AS total FROM {$server->loginDatabase}.login ";
if (Athena::config('HideTempBannedStats')) {
	$sql .= "WHERE unban_time <= UNIX_TIMESTAMP()";
}
if (Athena::config('HidePermBannedStats')) {
	if (Athena::config('HideTempBannedStats')) {
		$sql .= " AND state != 5";
	} else {
		$sql .= "WHERE state != 5";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['accounts'] += $sth->fetch()->total;

// Characters.
$sql = "SELECT COUNT(`char`.char_id) AS total FROM {$server->charMapDatabase}.`char` ";
if (Athena::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP()";
}
if (Athena::config('HidePermBannedStats')) {
	if (Athena::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['characters'] += $sth->fetch()->total;

// Guilds.
$sql = "SELECT COUNT(guild_id) AS total FROM {$server->charMapDatabase}.guild";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['guilds'] += $sth->fetch()->total;

// Parties.
$sql = "SELECT COUNT(party_id) AS total FROM {$server->charMapDatabase}.party";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['parties'] += $sth->fetch()->total;

// Zeny.
$sql = "SELECT SUM(`char`.zeny) AS total FROM {$server->charMapDatabase}.`char` ";
if ($hideLevel=Athena::config('InfoHideZenyLevel')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.group_id < ?";
	$bind = array($hideLevel);
}
if (Athena::config('HideTempBannedStats')) {
	if ($hideLevel) {
		$sql .= " AND unban_time <= UNIX_TIMESTAMP()";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE unban_time <= UNIX_TIMESTAMP()";
	}
}
if (Athena::config('HidePermBannedStats')) {
	if ($hideLevel || Athena::config('HideTempBannedStats')) {
		$sql .= " AND state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE state != 5";
	}
}

$sth = $server->connection->getStatement($sql);
$sth->execute($hideLevel ? $bind : array());
$info['zeny'] += $sth->fetch()->total;

// Job classes.
$sql = "SELECT `char`.class, COUNT(`char`.class) AS total FROM {$server->charMapDatabase}.`char` ";
if (Athena::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP() ";
}
if (Athena::config('HidePermBannedStats')) {
	if (Athena::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5 ";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5 ";
	}
}
$sql .= "GROUP BY `char`.class";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$classes = $sth->fetchAll();
if ($classes) {
	foreach ($classes as $class) {
		$classnum = (int)$class->class;
		$info['classes'][Athena::config("JobClasses.$classnum")] = $class->total;
	}
}

if (Athena::config('SortJobsByAmount')) {
	arsort($info['classes']);
}
?>