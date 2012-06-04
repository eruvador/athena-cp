<?php
if (!defined('ATHENA_ROOT')) exit;

require_once 'Athena/Captcha.php';
$captcha = new Athena_Captcha();
$session->setSecurityCodeData($captcha->code);
$captcha->display();
?>