<?php if (!defined('ATHENA_ROOT')) exit ?>
<h2><?php echo htmlspecialchars(Athena::message('HistoryPassResetHeading')) ?></h2>
<?php if ($resets): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('request_date', Athena::message('HistoryPassResetRequestDate')) ?></th>
		<th><?php echo $paginator->sortableColumn('request_ip', Athena::message('HistoryPassResetRequestIp')) ?></th>
		<th><?php echo $paginator->sortableColumn('reset_date', Athena::message('HistoryPassResetResetDate')) ?></th>
		<th><?php echo $paginator->sortableColumn('reset_ip', Athena::message('HistoryPassResetResetIp')) ?></th>
	</tr>
	<?php foreach ($resets as $reset): ?>
	<tr>
		<td><?php echo $this->formatDateTime($reset->request_date) ?></td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('last_ip' => $reset->request_ip), $reset->request_ip) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($reset->request_ip) ?>
		<?php endif ?>
		</td>
		<td>
			<?php if ($reset->reset_date): ?>
				<?php echo htmlspecialchars($reset->reset_date) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NeverLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($reset->reset_ip): ?>
				<?php if ($auth->actionAllowed('account', 'index')): ?>
					<?php echo $this->linkToAccountSearch(array('last_ip' => $reset->reset_ip), $reset->reset_ip) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($reset->reset_ip) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Athena::message('HistoryNoPassResets')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Athena::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>