<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('IpbanAddHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="addipban" value="1" />
	<table class="generic-form-table">
		<tr>
			<th><label for="list"><?php echo htmlspecialchars(Athena::message('IpbanIpAddressLabel')) ?></label></th>
			<td><input type="text" name="list" id="list" value="<?php echo htmlspecialchars($params->get('list')) ?>" /></td>
			<td><p><?php echo htmlspecialchars(Athena::message('IpbanIpAddressInfo')) ?></p></td>
		</tr>
		<tr>
			<th><label for="reason"><?php echo htmlspecialchars(Athena::message('IpbanReasonLabel')) ?></label></th>
			<td>
				<textarea name="reason" id="reason" class="reason"><?php echo htmlspecialchars($params->get('reason')) ?></textarea>
			</td>
			<td></td>
		</tr>
		<tr>
			<th><label><?php echo htmlspecialchars(Athena::message('IpbanUnbanDateLabel')) ?></label></th>
			<td><?php echo $this->dateTimeField('rtime', ($rtime=$params->get('rtime')) ? $rtime : null) ?></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2"><input type="submit" value="<?php echo htmlspecialchars(Athena::message('IpbanAddButton')) ?>" /></td>
		</tr>
	</table>
</form>