<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2 class="red"><?php echo htmlspecialchars(Athena::message('UnauthorizedHeading')) ?></h2>
<p><?php printf(Athena::message('UnauthorizedInfo'), $metaRefresh['location']) ?></p>