<?php

session_start();

include '../config/config.php';

include '../lib/database.php';

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$db = $connection->open();

include 'controller/init.php';

$connection->close($db);

if (isset($_SESSION['install_completed']))
{
	unset ($_SESSION['install_completed']);

	unlink ('../' . INSTALL_DIR . 'controller/init.php');
	unlink ('../' . INSTALL_DIR . 'controller/route.php');
	unlink ('../' . INSTALL_DIR . 'model/init.php');
	unlink ('../' . INSTALL_DIR . 'model/script.php');
	unlink ('../' . INSTALL_DIR . 'view/init.php');
	unlink ('../' . INSTALL_DIR . 'view/layout.php');
	unlink ('../' . INSTALL_DIR . 'index.php');
}

?>
