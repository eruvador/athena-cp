<?php if (!defined('Athena_ROOT')) exit; ?>
<h2><?php echo Athena::message('HelloWorld') ?></h2>
<p><?php echo Athena::message('HelloInfoText') ?></p>
<p><?php printf(Athena::message('HelloVersionText'), $athenaVersion) ?></p>