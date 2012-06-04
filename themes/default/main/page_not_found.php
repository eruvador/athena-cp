<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('PageNotFoundHeading')) ?></h2>
<p><?php echo htmlspecialchars(Athena::message('PageNotFoundInfo')) ?></p>
<p><span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>