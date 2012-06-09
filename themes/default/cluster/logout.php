<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('LogoutHeading')) ?></h2>
<p><strong><?php echo htmlspecialchars(Athena::message('LogoutInfo')) ?></strong> <?php printf(Athena::message('LogoutInfo2'), $metaRefresh['location']) ?></p>