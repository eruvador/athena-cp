<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('HistoryGameLoginHeading')) ?></h2>
<?php if ($logins): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Athena::message('HistoryLoginDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('ip', Athena::message('HistoryIpAddrLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('rcode', Athena::message('HistoryRepsCodeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('log', Athena::message('HistoryLogMessageLabel')) ?></th>
	</tr>
	<?php foreach ($logins as $login): ?>
	<tr>
		<td><?php echo $this->formatDateTime($login->time) ?></td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('last_ip' => $login->ip), $login->ip) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($login->ip) ?>
		<?php endif ?>
		</td>
		<td><?php echo $login->rcode ?></td>
		<td><?php echo htmlspecialchars($login->log) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Athena::message('HistoryNoGameLogins')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Athena::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>