<?php if (!defined('ATHENA_ROOT')) exit; ?>
<div class="credit-balance">
	<span class="balance-text">Donation Credits</span>
	<span class="balance-amount"><?php echo number_format((int)$session->account->balance) ?></span>
</div>