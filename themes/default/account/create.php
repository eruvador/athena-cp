<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('AccountCreateHeading')) ?></h2>
<p><?php printf(htmlspecialchars(Athena::message('AccountCreateInfo')), '<a href="'.$this->url('service', 'tos').'">'.Athena::message('AccountCreateTerms').'</a>') ?></p>
<?php if (Athena::config('RequireEmailConfirm')): ?>
<p><strong>Note:</strong> You will need to provide a working e-mail address to confirm your account before you can log-in.</p>
<?php endif ?>
<?php if (isset($errorMessage)): ?>
<p class="red" style="font-weight: bold"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->url ?>" method="post" class="generic-form">
	<?php if (count($serverNames) === 1): ?>
	<input type="hidden" name="server" value="<?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?>">
	<?php endif ?>
	<table class="generic-form-table">
		<?php if (count($serverNames) > 1): ?>
		<tr>
			<th><label for="register_server"><?php echo htmlspecialchars(Athena::message('AccountServerLabel')) ?></label></th>
			<td>
				<select name="server" id="register_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
				<?php foreach ($serverNames as $serverName): ?>
					<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
				<?php endforeach ?>
				</select>
			</td>
		</tr>
		<?php endif ?>
		
		<tr>
			<th><label for="register_username"><?php echo htmlspecialchars(Athena::message('AccountUsernameLabel')) ?></label></th>
			<td><input type="text" name="username" id="register_username" value="<?php echo htmlspecialchars($params->get('username')) ?>" /></td>
		</tr>
		
		<tr>
			<th><label for="register_password"><?php echo htmlspecialchars(Athena::message('AccountPasswordLabel')) ?></label></th>
			<td><input type="password" name="password" id="register_password" /></td>
		</tr>
		
		<tr>
			<th><label for="register_confirm_password"><?php echo htmlspecialchars(Athena::message('AccountPassConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirm_password" id="register_confirm_password" /></td>
		</tr>
		
		<tr>
			<th><label for="register_email_address"><?php echo htmlspecialchars(Athena::message('AccountEmailLabel')) ?></label></th>
			<td><input type="text" name="email_address" id="register_email_address" value="<?php echo htmlspecialchars($params->get('email_address')) ?>" /></td>
		</tr>
		
		<tr>
			<th><label for="register_birth_date"><?php echo htmlspecialchars(Athena::message('BirthDateLabel')) ?></label></th>
			<td><input type="text" name="birth_date" id="register_birth_date" value="<?php echo htmlspecialchars($params->get('birth_date')) ?>" /><i>(Format: YYYY-MM-DD)</i></td>
		</tr>
		
		<tr>
			<th><label><?php echo htmlspecialchars(Athena::message('AccountGenderLabel')) ?></label></th>
			<td>
				<p>
					<label><input type="radio" name="gender" id="register_gender_m" value="M"<?php if ($params->get('gender') === 'M') echo ' checked="checked"' ?> /> <?php echo $this->genderText('M') ?></label>
					<label><input type="radio" name="gender" id="register_gender_f" value="F"<?php if ($params->get('gender') === 'F') echo ' checked="checked"' ?> /> <?php echo $this->genderText('F') ?></label>
					<strong title="<?php echo htmlspecialchars(Athena::message('AccountCreateGenderInfo')) ?>">?</strong>
				</p>
			</td>
		</tr>
		
		<?php if (Athena::config('UseCaptcha')): ?>
		<tr>
			<?php if (Athena::config('EnableReCaptcha')): ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Athena::message('AccountSecurityLabel')) ?></label></th>
			<td><?php echo $recaptcha ?></td>
			<?php else: ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Athena::message('AccountSecurityLabel')) ?></label></th>
			<td>
				<div class="security-code">
					<img src="<?php echo $this->url('captcha') ?>" />
				</div>
				
				<input type="text" name="security_code" id="register_security_code" />
				<div style="font-size: smaller;" class="action">
					<strong><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Athena::message('RefreshSecurityCode')) ?></a></strong>
				</div>
			</td>
			<?php endif ?>
		</tr>
		<?php endif ?>
		
		<tr>
			<td></td>
			<td>
				<div style="margin-bottom: 5px">
					<?php printf(htmlspecialchars(Athena::message('AccountCreateInfo2')), '<a href="'.$this->url('service', 'tos').'">'.Athena::message('AccountCreateTerms').'</a>') ?>
				</div>
				<div>
					<button type="submit"><strong><?php echo htmlspecialchars(Athena::message('AccountCreateButton')) ?></strong></button>
				</div>
			</td>
		</tr>
	</table>
</form>