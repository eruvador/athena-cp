<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('DivorceHeading')) ?></h2>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="divorce" value="1" />
	<table class="generic-form-table">
		<tr>
			<td>
				<p>
				<?php echo htmlspecialchars(sprintf(Athena::message('DivorceText1'), $char->name)) ?>
				<?php if (!Athena::config('DivorceKeepChild')) echo htmlspecialchars(sprintf(Athena::message('DivorceText2'), $char->name)) ?>
				<?php if (!Athena::config('DivorceKeepRings')) echo htmlspecialchars(Athena::message('DivorceText3')) ?>
				</p>
			</td>
		</tr>
		<tr>
			<td><button type="submit"><strong><?php echo htmlspecialchars(Athena::message('DivorceButton')) ?></strong></button></td>
		</tr>
	</table>
</form>