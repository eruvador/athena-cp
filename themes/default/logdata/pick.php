<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('PickLogHeading')) ?></h2>
<?php if ($picks): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Athena::message('PickLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Athena::message('PickLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('type', Athena::message('PickLogTypeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('nameid', Athena::message('PickLogItemLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('amount', Athena::message('PickLogAmountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('refine', Athena::message('PickLogRefineLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('card0', Athena::message('PickLogCard0Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card1', Athena::message('PickLogCard1Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card2', Athena::message('PickLogCard2Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card3', Athena::message('PickLogCard3Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Athena::message('PickLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($picks as $pick): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($pick->time) ?></td>
		<td>
			<?php if ($pick->char_name): ?>
				<?php if ($pick->type == 'M' || $pick->type == 'L'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($pick->char_id, $pick->char_name) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($pick->char_name) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($pick->char_id, $pick->char_name) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($pick->char_name) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php elseif ($pick->char_id): ?>
				<?php if ($pick->type == 'M' || $pick->type == 'L'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($pick->char_id, $pick->char_id) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($pick->char_id) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($pick->char_id, $pick->char_id) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($pick->char_id) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->pick_type): ?>
				<?php echo htmlspecialchars($pick->pick_type) ?>
			<?php elseif ($pick->type): ?>
				<?php echo $pick->type ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->item_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->nameid, $pick->item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->item_name) ?>
				<?php endif ?>
			<?php elseif ($pick->nameid): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->nameid, $pick->nameid) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->nameid) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo $pick->amount >= 0 ? '+'.number_format($pick->amount) : number_format($pick->amount) ?></td>
		<td><?php echo $pick->refine ?></td>
		<!-- Card0 -->
		<td>
			<?php if ($pick->card0_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card0): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card1 -->
		<td>
			<?php if ($pick->card1_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card1): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card2 -->
		<td>
			<?php if ($pick->card2_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card2): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card3 -->
		<td>
			<?php if ($pick->card3_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card3): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->map): ?>
				<?php echo htmlspecialchars(basename($pick->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Athena::message('PickLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Athena::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>