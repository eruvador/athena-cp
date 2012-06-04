<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('MainPageHeading')) ?></h2>
<p><strong><?php echo htmlspecialchars(Athena::message('MainPageInfo')) ?></strong></p>
<p><?php echo htmlspecialchars(Athena::message('MainPageInfo2')) ?></p>
<ol>
	<li><p class="green"><?php echo htmlspecialchars(sprintf(Athena::message('MainPageStep1'), __FILE__)) ?></p></li>
	<li><p class="green"><?php echo htmlspecialchars(Athena::message('MainPageStep2')) ?></p></li>
</ol>
<p style="text-align: right"><strong><em><?php echo htmlspecialchars(Athena::message('MainPageThanks')) ?></em></strong></p>
