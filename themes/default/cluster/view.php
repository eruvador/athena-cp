<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('ClusterViewHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<h3><?php echo htmlspecialchars(Athena::message('ClusterViewAccount')) ?></h3>
<?php if ($cluster): ?>
<table class="vertical-table">
	<?php if ($auth->allowedToSeeClusterID): ?>
	<tr>
		<th><?php echo htmlspecialchars(Athena::message('ClusterIdLabel')) ?></th>
		<td><?php echo $cluster->cluster_id ?></td>
	</tr>
	<?php endif ?>
	<tr>
		<th><?php echo htmlspecialchars(Athena::message('UsernameLabel')) ?></th>
		<td><?php echo $cluster->username ?></td>
	</tr>
	<tr>
		<th><?php echo htmlspecialchars(Athena::message('EmailAddressLabel')) ?></th>
		<td>
			<?php if ($cluster->email): ?>
				<?php echo htmlspecialchars($cluster->email) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th><?php echo htmlspecialchars(Athena::message('ClusterBirthdateLabel')) ?></th>
		<td><?php echo htmlspecialchars($cluster->birthdate) ?></td>
	</tr>
</table>

<h3><?php echo htmlspecialchars(Athena::message('ClusterLinkedAcc')) ?></h3>
<?php if ($links): ?>
	<?php foreach ($links as $account): ?>
		<table class="vertical-table">
			<?php if ($auth->allowedToSeeAccountID): ?>
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('AccountIdLabel')) ?></th>
				<td><?php echo $account->account_id ?></td>
			</tr>
			<?php endif ?>
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('UsernameLabel')) ?></th>
				<td><?php echo $this->linkToAccount($account->account_id, $account->userid) ?></td>
			</tr>
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('GenderLabel')) ?></th>
				<td>
					<?php if ($gender = $this->genderText($account->sex)): ?>
						<?php echo $gender ?>
					<?php else: ?>
						<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('ClusterStateLabel')) ?></th>
				<td>
					<?php if (!$account->confirmed && $account->confirm_code): ?>
						<span class="account-state state-pending">
							<?php echo htmlspecialchars(Athena::message('ClusterStatePending')) ?>
						</span>
					<?php else: ?>
						<?php echo htmlspecialchars(Athena::message('ClusterStateNormal')) ?>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('LastLoginDateLabel')) ?></th>
				<td>
					<?php if (!$account->lastlogin || $account->lastlogin == '0000-00-00 00:00:00'): ?>
						<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NeverLabel')) ?></span>
					<?php else: ?>
						<?php echo $this->formatDateTime($account->lastlogin) ?>
					<?php endif ?>
				</td>
			</tr>
		</table>
		<br/>
	<?php endforeach ?>
<?php else: ?>
	<p><?php echo htmlspecialchars(Athena::message('ClusterLinkNone')) ?></p>
<?php endif ?>

<?php else: ?>
<p><?php echo htmlspecialchars(Athena::message('ClusterViewNotFound')) ?></p>
<?php endif ?>