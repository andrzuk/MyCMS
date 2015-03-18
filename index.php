<?php

session_start();

include 'config/config.php';
include LIB_DIR . 'database.php';

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$db = $connection->open();

include LIB_DIR . 'settings.php';
include LIB_DIR . 'visitors.php';

if (isset($_GET['route'])) 
{
	$routing = explode('&', $_GET['route']);
	$route_controller = APP_DIR . 'controller/' . trim($routing[0]) . '.php';

	if (isset($_SESSION['last_route']))
	{
		if ($_GET['route'] == $_SESSION['last_route']) $_SESSION['keep_paginator'] = TRUE;
		else unset($_SESSION['keep_paginator']);
	}
}
else
{
	$route_controller = APP_DIR . 'controller/index.php';
}

if (!file_exists($route_controller)) 
{
	$route_controller = APP_DIR . 'controller/not_found.php';
}

include APP_DIR . 'controller/main/status.php';
		
include APP_DIR . 'controller/main/acl.php';

if (!file_exists('install/index.php')) 
{
	include $route_controller;
}
else
{
	$installation = TRUE;
	include APP_DIR . 'controller/index.php';
}

$_SESSION['last_route'] = isset($_GET['route']) ? $_GET['route'] : NULL;

$visitor = new Visitors($db);
$visitor->register();

$connection->close($db);

?>
