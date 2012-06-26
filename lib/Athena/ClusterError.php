<?php
require_once 'Athena/Error.php';

class Athena_ClusterError extends Athena_Error {
	const USERNAME_ALREADY_TAKEN = 0;
	const USERNAME_TOO_SHORT     = 1;
	const USERNAME_TOO_LONG      = 2;
	const PASSWORD_TOO_SHORT     = 3;
	const PASSWORD_TOO_LONG      = 4;
	const PASSWORD_MISMATCH      = 5;
	const EMAIL_ADDRESS_IN_USE   = 6;
	const INVALID_EMAIL_ADDRESS  = 7;
	const INVALID_SECURITY_CODE  = 8;
	const INVALID_USERNAME       = 9;
	
	const UNEXPECTED             = 10;
	const INVALID_LINK           = 11;
}
?>