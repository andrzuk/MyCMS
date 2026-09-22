<?php

session_start();

error_reporting(E_ALL);

if (isset($_SESSION['last_request_timestamp']))
{
	if (microtime(true) - $_SESSION['last_request_timestamp'] < 0.1) exit();
}
$_SESSION['last_request_timestamp'] = microtime(true);

$visitor_ip = $_SERVER['REMOTE_ADDR'];

// IP-based rate limiting
if (!isset($_SESSION['ip_last_request_timestamp'])) 
{
	$_SESSION['ip_last_request_timestamp'] = [];
}
if (isset($_SESSION['ip_last_request_timestamp'][$visitor_ip])) 
{
	if (microtime(true) - $_SESSION['ip_last_request_timestamp'][$visitor_ip] < 0.1) exit();
}
$_SESSION['ip_last_request_timestamp'][$visitor_ip] = microtime(true);

include 'config/config.php';
include LIB_DIR . 'database.php';

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$db = $connection->open();

include LIB_DIR . 'settings.php';
include LIB_DIR . 'visitors.php';

$setting = new Settings($db);
$visitor = new Visitors($db);

$black_list_visitors = $setting->get_config_key('black_list_visitors');
$black_list_ip = explode(',', $black_list_visitors);
foreach ($black_list_ip AS $black_list_item)
	if ($visitor_ip == trim(str_replace('\'', '', $black_list_item)))
	{
		$visitor->reject();
		$connection->close($db);
		http_response_code(401);
		exit;
	}

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

$visitor->register();

$connection->close($db);

?>
