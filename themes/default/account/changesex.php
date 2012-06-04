<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('GenderChangeHeading')) ?></h2>
<?php if ($cost): ?>
<p>
	<?php printf(Athena::message('GenderChangeCost'), '<span class="remaining-balance">'.number_format((int)$cost).'</span>') ?>
	<?php printf(Athena::message('GenderChangeBalance'), '<span class="remaining-balance">'.number_format((int)$session->account->balance).'</span>') ?>
</p>
<?php if (!$hasNecessaryFunds): ?>
<p><?php echo htmlspecialchars(Athena::message('GenderChangeNoFunds')) ?></p>
<?php elseif ($auth->allowedToAvoidSexChangeCost): ?>
<p><?php echo htmlspecialchars(Athena::message('GenderChangeNoCost')) ?></p>
<?php endif ?>
<?php endif ?>

<?php if ($hasNecessaryFunds): ?>
<?php if (empty($errorMessage)): ?>
<p><strong><?php echo htmlspecialchars(Athena::message('NoteLabel')) ?>:</strong> <?php printf(Athena::message('GenderChangeCharInfo'), '<em>'.implode(', ', array_values($badJobs)).'</em>') ?>.</p>
<h3><?php echo htmlspecialchars(Athena::message('GenderChangeSubHeading')) ?></h3>
<?php else: ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="changegender" value="1" />
	<table class="generic-form-table">
		<tr>
			<td>
				<p>
					<?php printf(Athena::message('GenderChangeFormText'), '<strong>'.strtolower($this->genderText($session->account->sex == 'M' ? 'F' : 'M')).'</strong>') ?>
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>
					<button type="submit"
						onclick="return confirm('<?php echo str_replace("\'", "\\'", Athena::message('GenderChangeConfirm')) ?>')">
							<strong><?php echo htmlspecialchars(Athena::message('GenderChangeButton')) ?></strong>
					</button>
				</p>
			</td>
		</tr>
	</table>
</form>
<?php endif ?>