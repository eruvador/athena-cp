<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('ServerStatusHeading')) ?></h2>
<p><?php echo htmlspecialchars(Athena::message('ServerStatusInfo')) ?></p>
<?php foreach ($serverStatus as $privServerName => $gameServers): ?>
<h3>Server Status for <?php echo htmlspecialchars($privServerName) ?></h3>
<table id="server_status">
	<tr>
		<td class="status"><?php echo htmlspecialchars(Athena::message('ServerStatusServerLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Athena::message('ServerStatusLoginLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Athena::message('ServerStatusCharLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Athena::message('ServerStatusMapLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Athena::message('ServerStatusOnlineLabel')) ?></td>
	</tr>
	<?php foreach ($gameServers as $serverName => $gameServer): ?>
	<tr>
		<th class="server"><?php echo htmlspecialchars($serverName) ?></th>
		<td class="status"><?php echo $this->serverUpDown($gameServer['loginServerUp']) ?></td>
		<td class="status"><?php echo $this->serverUpDown($gameServer['charServerUp']) ?></td>
		<td class="status"><?php echo $this->serverUpDown($gameServer['mapServerUp']) ?></td>
		<td class="status"><?php echo $gameServer['playersOnline'] ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php endforeach ?>