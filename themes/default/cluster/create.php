<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('ClusterCreateHeading')) ?></h2>
<p><?php printf(htmlspecialchars(Athena::message('ClusterCreateInfo')), '<a href="'.$this->url('service', 'tos').'">'.Athena::message('ClusterCreateTerms').'</a>') ?></p>
<?php if (Athena::config('RequireEmailConfirm')): ?>
<p><strong>Note:</strong> You will need to provide a working e-mail address to confirm your account before you can log-in.</p>
<?php endif ?>
<?php if (isset($errorMessage)): ?>
<p class="red" style="font-weight: bold"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>

<?php include('tos.php')?>

<form action="<?php echo $this->url ?>" method="post" class="generic-form">
	<table class="generic-form-table">	
		<tr>
			<th><label for="register_username"><?php echo htmlspecialchars(Athena::message('ClusterUsernameLabel')) ?></label></th>
			<td><input type="text" name="username" id="register_username" value="<?php echo htmlspecialchars($params->get('username')) ?>" /></td>
		</tr>
		
		<tr>
			<th><label for="register_password"><?php echo htmlspecialchars(Athena::message('ClusterPasswordLabel')) ?></label></th>
			<td><input type="password" name="password" id="register_password" /></td>
		</tr>
		
		<tr>
			<th><label for="register_confirm_password"><?php echo htmlspecialchars(Athena::message('ClusterPassConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirm_password" id="register_confirm_password" /></td>
		</tr>
		
		<tr>
			<th><label for="register_email_address"><?php echo htmlspecialchars(Athena::message('ClusterEmailLabel')) ?></label></th>
			<td><input type="text" name="email_address" id="register_email_address" value="<?php echo htmlspecialchars($params->get('email_address')) ?>" /></td>
		</tr>
		
		<tr>
			<th><label for="register_birth_date"><?php echo htmlspecialchars(Athena::message('BirthDateLabel')) ?></label></th>
			<td>
				<select name="date_day">
					<?php for ($day = 1; $day <= 31; $day++): ?>
						<option value="<?php echo $day ?>"><?php echo $day ?></option>
					<?php endfor ?>
				</select>
				<select name="date_month">
					<?php foreach ($months as $m => $month): ?>
						<option value="<?php echo $m ?>"><?php echo $month ?></option>
					<?php endforeach ?>
				</select>
				<select name="date_year">
					<?php for ($year; $year < $endYear; $year++): ?>
						<option value="<?php echo $year ?>"><?php echo $year ?></option>
					<?php endfor ?>
				</select>
			</td>
		</tr>

		<?php if (Athena::config('UseCaptcha')): ?>
		<tr>
			<?php if (Athena::config('EnableReCaptcha')): ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Athena::message('ClusterSecurityLabel')) ?></label></th>
			<td><?php echo $recaptcha ?></td>
			<?php else: ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Athena::message('ClusterSecurityLabel')) ?></label></th>
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
					<?php printf(htmlspecialchars(Athena::message('ClusterCreateInfo2')), Athena::message('ClusterCreateTerms')) ?>
				</div>
				<div>
					<button type="submit"><strong><?php echo htmlspecialchars(Athena::message('ClusterCreateButton')) ?></strong></button>
				</div>
			</td>
		</tr>
	</table>
</form>