<?php

include_once '../config/config.php';

$help_init_file = dirname(__FILE__) . '/../' . HELP_DIR . 'controller/init.php';
$install_init_file = dirname(__FILE__) . '/../' . INSTALL_DIR . 'controller/init.php';

if (isset($connection_error))
{
	if (file_exists($help_init_file)) include $help_init_file;
}
else
{
	if (file_exists($install_init_file)) include $install_init_file;
	else
	{
		if (file_exists($help_init_file)) include $help_init_file;
	}
}

?>
