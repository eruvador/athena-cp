<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('ClusterLinkHeading')) ?></h2>
<?php if (isset($errorMessage)): ?>
<p class="red" style="font-weight: bold"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>

<form action="<?php echo $this->url ?>" method="post" class="generic-form">
	<table class="generic-form-table">	
		<tr>
			<th><label for="link_username"><?php echo htmlspecialchars(Athena::message('ClusterUsernameLabel')) ?></label></th>
			<td><input type="text" name="username" id="link_username" value="<?php echo htmlspecialchars($params->get('username')) ?>" /></td>
		</tr>
		
		<tr>
			<th><label for="link_password"><?php echo htmlspecialchars(Athena::message('ClusterPasswordLabel')) ?></label></th>
			<td><input type="password" name="password" id="link_password" /></td>
		</tr>
		
		<tr>
			<th><label for="register_confirm_password"><?php echo htmlspecialchars(Athena::message('ClusterPassConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirm_password" id="register_confirm_password" /></td>
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
				<button type="submit"><strong><?php echo htmlspecialchars(Athena::message('ClusterLinkButton')) ?></strong></button>
			</td>
		</tr>
	</table>
</form>