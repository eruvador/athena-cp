<?php if (!defined('ATHENA_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Athena::message('MissingViewHeading')) ?></h2>
<p><?php echo htmlspecialchars(Athena::message('MissingViewModLabel')) ?> <span class="module-name"><?php echo $this->params->get('module') ?></span>, <?php echo htmlspecialchars(Athena::message('MissingViewActLabel')) ?> <span class="module-name"><?php echo $this->params->get('action') ?></span></p>
<p><?php echo htmlspecialchars(Athena::message('MissingViewReqLabel')) ?> <span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>
<p><?php echo htmlspecialchars(Athena::message('MissingViewLocLabel')) ?> <span class="fs-path"><?php echo $realViewPath ?></span></p>