<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'rejectors');

$content_title = 'Odrzucenia żądań';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Rejectors_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Rejectors_View($db);

$list_columns = array(
	array('db_name' => 'id', 				'column_name' => 'Id', 					'sorting' => 1),
	array('db_name' => 'visitor_ip', 		'column_name' => 'Host - Adres',		'sorting' => 1),
	array('db_name' => 'request_uri', 		'column_name' => 'Adres wywołany', 		'sorting' => 1),
	array('db_name' => 'visited', 			'column_name' => 'Godzina', 			'sorting' => 1),
);

if (isset($_POST['ListSearchButton']))
{
	$_SESSION['list_filter'] = htmlspecialchars(substr(trim($_POST['ListSearchText']), 0, 32));
}
if (isset($_POST['ListSearchClose']))
{
	$_SESSION['list_filter'] = NULL;
}

$date_from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date("Y-m-d");
$date_to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date("Y-m-d");
$days_range = isset($_SESSION['days_range']) ? $_SESSION['days_range'] : 10;

if (isset($_POST['date_from']))
{
	$date_from = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['date_from']) ? trim($_POST['date_from']) : date("Y-m-d");
}
$_SESSION['date_from'] = $date_from;

if (isset($_POST['date_to']))
{
	$date_to = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['date_to']) ? trim($_POST['date_to']) : date("Y-m-d");
}
$_SESSION['date_to'] = $date_to;

if (isset($_POST['days_range']))
{
	$days_range = $_POST['days_range'];
}
$_SESSION['days_range'] = $days_range;

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array();

// komplet danych przekazywanych do głównego operatora:

$objects = array(
	'model_object' => $model_object,
	'view_object' => $view_object,
	'record_object' => $record_object,
	'navi_object' => $navi_object,
	'db_params' => $db_params,
	'list_params' => $list_params,
	'list_columns' => $list_columns,
	'data_import' => $data_import,
);

include APP_DIR . 'controller/main/operator.php';

$controller_object = new Operator($objects);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = array (
	array (
		'address' => 'index.php?route=visitors',
		'caption' => 'Odwiedziny',
		'icon' => 'img/globe.png'
	),
	array (
		'address' => 'index.php?route=admin',
		'caption' => 'Zamknij',
		'icon' => 'img/stop.png'
	),
);

$status = new Status($db);
$user_status = $status->get_value('user_status');

$access = array(ADMIN, OPERATOR);

$acl = new AccessControlList(MODULE_NAME, $db);

if (in_array($user_status, $access) && $acl->available()) // są uprawnienia
{
	$record_list = $model_object->GetAll(NULL, $db_params);
	$navi_object->update($model_object, $list_params);
	$component_left = $view_object->ShowList($record_list, $list_columns, $list_params);

	$record_object = $model_object->GetSummaryData($days_range);
	$component_right = $view_object->ShowSummaryChart($record_object);

	$record_object = $model_object->GetStatsData($days_range);
	$component_below = $view_object->ShowStatsReport($record_object);

	$site_content = $view_object->ShowComponents($component_left, $component_right, $component_below);

	$site_message = $controller_object->Get('site_message');
	$site_dialog = $controller_object->Get('site_dialog');
}
else // brak uprawnień
{
	$content_options = $page_options->get_options(NULL);

	$site_dialog = array(
		'ERROR',
		'Brak uprawnień',
		'Uruchomiona funkcja wymaga zalogowania do serwisu na konto o profilu administratora lub posiadania uprawnień określonych za pomocą systemu Access Control List.',
		array(
			array(
				'index.php?route=login', 'Zaloguj'
			),
			array(
				'index.php', 'Zamknij'
			),
		)
	);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
