<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2>Accounts</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo htmlspecialchars(Athena::message('SearchLabel')) ?></a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="account_id"><?php echo htmlspecialchars(Athena::message('AccountIdLabel')) ?>:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id')) ?>" />
		...
		<label for="username"><?php echo htmlspecialchars(Athena::message('UsernameLabel')) ?>:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username')) ?>" />
		<?php if ($searchPassword): ?>
		...
		<label for="password"><?php echo htmlspecialchars(Athena::message('PasswordLabel')) ?>:</label>
		<input type="text" name="password" id="password" value="<?php echo htmlspecialchars($params->get('password')) ?>" />
		<?php endif ?>
		...
		<label for="email"><?php echo htmlspecialchars(Athena::message('EmailAddressLabel')) ?>:</label>
		<input type="text" name="email" id="email" value="<?php echo htmlspecialchars($params->get('email')) ?>" />
		...
		<label for="last_ip"><?php echo htmlspecialchars(Athena::message('LastUsedIpLabel')) ?>:</label>
		<input type="text" name="last_ip" id="last_ip" value="<?php echo htmlspecialchars($params->get('last_ip')) ?>" />
		...
		<label for="gender"><?php echo htmlspecialchars(Athena::message('GenderLabel')) ?>:</label>
		<select name="gender" id="gender">
			<option value=""<?php if (!in_array($gender=$params->get('gender'), array('M', 'F'))) echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AllLabel')) ?></option>
			<option value="M"<?php if ($gender == 'M') echo ' selected="selected"' ?>><?php echo $this->genderText('M') ?></option>
			<option value="F"<?php if ($gender == 'F') echo ' selected="selected"' ?>><?php echo $this->genderText('F') ?></option>
		</select>
	</p>
	<p>
		<label for="account_state"><?php echo htmlspecialchars(Athena::message('AccountStateLabel')) ?>:</label>
		<select name="account_state" id="account_state">
			<option value=""<?php if (!($account_state=$params->get('account_state'))) echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AllLabel')) ?></option>
			<option value="normal"<?php if ($account_state == 'normal') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AccountStateNormal')) ?></option>
			<option value="pending"<?php if ($account_state == 'pending') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AccountStatePending')) ?></option>
			<option value="banned"<?php if ($account_state == 'banned') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AccountStateTempBanLbl')) ?></option>
			<option value="permabanned"<?php if ($account_state == 'permabanned') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('AccountStatePermBanned')) ?></option>
		</select>
		...
		<label for="account_level"><?php echo htmlspecialchars(Athena::message('AccountLevelLabel')) ?>:</label>
		<select name="account_level_op">
			<option value="eq"<?php if (($account_level_op=$params->get('account_level_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($account_level_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($account_level_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="account_level" id="account_level" value="<?php echo htmlspecialchars($params->get('account_level')) ?>" />
		...
		<label for="balance"><?php echo htmlspecialchars(Athena::message('CreditBalanceLabel')) ?>:</label>
		<select name="balance_op">
			<option value="eq"<?php if (($balance_op=$params->get('balance_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($balance_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($balance_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="balance" id="balance" value="<?php echo htmlspecialchars($params->get('balance')) ?>" />
	</p>
	<p>
		<label for="logincount"><?php echo htmlspecialchars(Athena::message('LoginCountLabel')) ?>:</label>
		<select name="logincount_op">
			<option value="eq"<?php if (($logincount_op=$params->get('logincount_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($logincount_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($logincount_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Athena::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="logincount" id="logincount" value="<?php echo htmlspecialchars($params->get('logincount')) ?>" />
		...
		<label for="use_last_login_after"><?php echo htmlspecialchars(Athena::message('LoginBetweenLabel')) ?>:</label>
		<input type="checkbox" name="use_last_login_after" id="use_last_login_after"<?php if ($params->get('use_last_login_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('last_login_after') ?>
		<label for="use_last_login_before">&mdash;</label>
		<input type="checkbox" name="use_last_login_before" id="use_last_login_before"<?php if ($params->get('use_last_login_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('last_login_before') ?>		
		
		<input type="submit" value="<?php echo htmlspecialchars(Athena::message('SearchButton')) ?>" />
		<input type="button" value="<?php echo htmlspecialchars(Athena::message('ResetButton')) ?>" onclick="reload()" />
	</p>
</form>
<?php if ($accounts): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('login.account_id', Athena::message('AccountIdLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('login.userid', Athena::message('UsernameLabel')) ?></th>
		<?php if ($showPassword): ?><th><?php echo $paginator->sortableColumn('login.user_pass', Athena::message('PasswordLabel')) ?></th><?php endif ?>
		<th><?php echo $paginator->sortableColumn('login.sex', Athena::message('GenderLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('group_id', Athena::message('AccountLevelLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('state', Athena::message('AccountStateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('balance', Athena::message('CreditBalanceLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('login.email', Athena::message('EmailAddressLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('logincount', Athena::message('LoginCountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('lastlogin', Athena::message('LastLoginDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('last_ip', Athena::message('LastUsedIpLabel')) ?></th>
		<!-- <th><?php echo $paginator->sortableColumn('reg_date', 'Register Date') ?></th> -->
	</tr>
	<?php foreach ($accounts as $account): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToAccount($account->account_id, $account->account_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($account->account_id) ?>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($account->userid) ?></td>
		<?php if ($showPassword): ?><td><?php echo htmlspecialchars($account->user_pass) ?></td><?php endif ?>
		<td>
			<?php if ($gender = $this->genderText($account->sex)): ?>
				<?php echo htmlspecialchars($gender) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo (int)$account->group_id ?></td>
		<td>
			<?php if (!$account->confirmed && $account->confirm_code): ?>
				<span class="account-state state-pending">
					<?php echo htmlspecialchars(Athena::message('AccountStatePending')) ?>
				</span>
			<?php elseif (($state = $this->accountStateText($account->state)) && !$account->unban_time): ?>
				<?php echo $state ?>
			<?php elseif ($account->unban_time): ?>
				<span class="account-state state-banned">
					<?php echo htmlspecialchars(sprintf(Athena::message('AccountStateTempBanned'), date(Athena::config('DateTimeFormat'), $account->unban_time))) ?>
				</span>
			<?php else: ?>
				<span class="account-state state-unknown"><?php echo htmlspecialchars(Athena::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$account->balance) ?></td>
		<td>
			<?php if ($account->email): ?>
				<?php echo $this->linkToAccountSearch(array('email' => $account->email), $account->email) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$account->logincount) ?></td>
		<td>
			<?php if (!$account->lastlogin || $account->lastlogin == '0000-00-00 00:00:00'): ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NeverLabel')) ?></span>
			<?php else: ?>
				<?php echo $this->formatDateTime($account->lastlogin) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($account->last_ip): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $account->last_ip), $account->last_ip) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Athena::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- <td>
			<?php if (!$account->reg_date || $account->reg_date == '0000-00-00 00:00:00'): ?>
				<span class="not-applicable">Unknown</span>
			<?php else: ?>
				<?php echo $this->formatDateTime($account->reg_date) ?>
			<?php endif ?>
		</td> -->
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Athena::message('AccountIndexNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Athena::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>