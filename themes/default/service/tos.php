<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('TermsHeading')) ?></h2>
<p style="font-style: italic"><?php echo htmlspecialchars(Athena::message('TermsInfo')) ?></p>
<p class="note"><?php echo htmlspecialchars(sprintf(Athena::message('TermsInfo2'), __FILE__)) ?></p>