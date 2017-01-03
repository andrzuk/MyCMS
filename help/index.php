<?php

include_once dirname(__FILE__) . '/../config/config.php';

$help_init_file = dirname(__FILE__) . '/../' . HELP_DIR . 'controller/init.php';
$install_init_file = dirname(__FILE__) . '/../' . INSTALL_DIR . 'controller/init.php';

if (file_exists($install_init_file)) // tryb instalacji
{
	$content_title = 'Instalacja serwisu';
	$install_exists = TRUE;
	include $install_init_file;
}
else // tryb normalnej pracy
{
	if (file_exists($help_init_file)) 
	{
		$content_title = 'Błąd połączenia z serwerem';
		$install_exists = FALSE;
		include $help_init_file;
	}
}

?>
