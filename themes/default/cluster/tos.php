<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h3><?php echo htmlspecialchars(Athena::message('TermsHeading')) ?></h3>
<p style="font-style: italic"><?php echo htmlspecialchars(Athena::message('TermsInfo')) ?></p>

<div style="max-height: 200px; overflow-y: auto">
	<p class="note"><?php echo htmlspecialchars(sprintf(Athena::message('TermsInfo2'), __FILE__)) ?></p>
</div>