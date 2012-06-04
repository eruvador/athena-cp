<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('AccountEditHeading')) ?></h2>
<?php if ($account): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post">
		<table class="vertical-table">
			<tr>
				<th><?php echo htmlspecialchars(Athena::message('UsernameLabel')) ?></th>
				<td><?php echo $account->userid ?></td>
				<th><?php echo htmlspecialchars(Athena::message('AccountIdLabel')) ?></th>
				<td><?php echo $account->account_id ?></td>
			</tr>
			<tr>
				<th><label for="email"><?php echo htmlspecialchars(Athena::message('EmailAddressLabel')) ?></label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlspecialchars($account->email) ?>" /></td>
				<?php if ($auth->allowedToEditAccountLevel && !$isMine): ?>
					<th><label for="level"><?php echo htmlspecialchars(Athena::message('AccountLevelLabel')) ?></label></th>
					<td><input type="text" name="group_id" id="level" value="<?php echo (int)$account->group_id ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Athena::message('AccountLevelLabel')) ?></th>
					<td>
						<input type="hidden" name="group_id" value="<?php echo (int)$account->group_id ?>" />
						<?php echo number_format((int)$account->group_id) ?>
					</td>
				<?php endif ?>
			</tr>
			<tr>
				<th><label for="gender"><?php echo htmlspecialchars(Athena::message('GenderLabel')) ?></label></th>
				<td>
					<select name="gender" id="gender">
						<option value="M"<?php if ($account->sex == 'M') echo ' selected="selected"' ?>><?php echo $this->genderText('M') ?></option>
						<option value="F"<?php if ($account->sex == 'F') echo ' selected="selected"' ?>><?php echo $this->genderText('F') ?></option>
					</select>
				</td>
				<th><?php echo htmlspecialchars(Athena::message('AccountStateLabel')) ?></th>
				<td>
					<?php if (($state = $this->accountStateText($account->state)) && !$account->unban_time): ?>
						<?php echo $state ?>
					<?php elseif ($account->unban_time): ?>
						<span class="account-state state-banned">
							Banned Until
							<?php echo htmlspecialchars(date(Athena::config('DateTimeFormat'), $account->unban_time)) ?>
						</span>
					<?php else: ?>
						<span class="account-state state-unknown"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th><label for="logincount"><?php echo htmlspecialchars(Athena::message('LoginCountLabel')) ?></label></th>
				<td><input type="text" name="logincount" id="logincount" value="<?php echo (int)$account->logincount ?>" /></td>
				<?php if ($auth->allowedToEditAccountBalance): ?>
					<th><label for="balance"><?php echo htmlspecialchars(Athena::message('CreditBalanceLabel')) ?></label></th>
					<td><input type="text" name="balance" id="balance" value="<?php echo (int)$account->balance ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Athena::message('CreditBalanceLabel')) ?></th>
					<td><?php echo number_format((int)$account->balance) ?></td>
				<?php endif ?>
			</tr>
			<tr>
				<th><label for="use_lastlogin"><?php echo htmlspecialchars(Athena::message('LastLoginDateLabel')) ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="use_lastlogin" id="use_lastlogin" />
					<?php echo $this->dateTimeField('lastlogin', $account->lastlogin) ?>
				</td>
			</tr>
			<tr>
				<th><label for="last_ip"><?php echo htmlspecialchars(Athena::message('LastUsedIpLabel')) ?></label></th>
				<td colspan="3"><input type="text" name="last_ip" id="last_ip" value="<?php echo htmlspecialchars($account->last_ip) ?>" /></td>
			</tr>
			<tr>
				<td colspan="4" align="right">
					<input type="submit" value="<?php echo htmlspecialchars(Athena::message('AccountEditButton')) ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Athena::message('AccountEditNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Athena::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>