<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'visitors');

$content_title = 'Odwiedziny serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Visitors_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Visitors_View($db);

$status = new Status($db);
$user_status = $status->get_value('user_status');

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$access = array(ADMIN, OPERATOR);

$acl = new AccessControlList(MODULE_NAME, $db);
			
if (in_array($user_status, $access) && $acl->available()) // są uprawnienia
{
	if (isset($_POST['run_button'])) // podano nowe parametry
	{
		$period_from_year = isset($_POST['period_from_year']) ? $_POST['period_from_year'] : NULL;
		$period_from_month = isset($_POST['period_from_month']) ? $_POST['period_from_month'] : NULL;
		$period_from_day = isset($_POST['period_from_day']) ? $_POST['period_from_day'] : NULL;
		$period_to_year = isset($_POST['period_to_year']) ? $_POST['period_to_year'] : NULL;
		$period_to_month = isset($_POST['period_to_month']) ? $_POST['period_to_month'] : NULL;
		$period_to_day = isset($_POST['period_to_day']) ? $_POST['period_to_day'] : NULL;
		$exceptions = isset($_POST['exceptions']) ? $_POST['exceptions'] : NULL;

		$filters = array();
		$elements = array(false, false, false);
		foreach ($_POST as $key => $val)
		{
			if (substr($key, 0, 6) == 'field-') { $field = $val; $elements[0] = true; }
			if (substr($key, 0, 9) == 'operator-') { $operator = $val; $elements[1] = true; }
			if (substr($key, 0, 6) == 'value-') { $value = $val; $elements[2] = true; }
			if ($elements[0] && $elements[1] && $elements[2])
			{
				$filters[] = array('field' => $field, 'operator' => $operator, 'value' => $value);
				$elements[0] = false; $elements[1] = false; $elements[2] = false;
			}
		}
		
		$params_record = array(
			'period_from' => $period_from_year .'-'. $period_from_month .'-'. $period_from_day,
			'period_to' => $period_to_year .'-'. $period_to_month .'-'. $period_to_day,
			'filters' => $filters,
			'exceptions' => $exceptions,
		);
		
		// zapisuje parametry:
		$result = $model_object->SetParams($params_record);	
		
		unset($_SESSION['keep_paginator']);
	}

	$list_columns = array(
		array('db_name' => 'id', 				'column_name' => 'Id', 					'sorting' => 1),
		array('db_name' => 'visitor_ip', 		'column_name' => 'Host - Adres',		'sorting' => 1),
		array('db_name' => 'http_referer', 		'column_name' => 'Adres odwoławczy',	'sorting' => 1),
		array('db_name' => 'request_uri', 		'column_name' => 'Adres wywołany', 		'sorting' => 1),
		array('db_name' => 'visited', 			'column_name' => 'Godzina', 			'sorting' => 1),
	);

	include 'main/navi.php';

	$navi_object = new Navi($db);

	$navi_params = $navi_object->init($list_columns);

	$record_object = $navi_params['record_object'];
	$db_params = $navi_params['db_params'];
	$list_params = $navi_params['list_params'];

	$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

	// dane z bazy potrzebne do kontrolek formularza:

	$data_import = array();

	// brakujące pola:
	
	$failed = array();
	
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

	$content_options = array(
		array(
			'address' => 'index.php?route=admin',
			'caption' => 'Zamknij',
			'icon' => 'img/stop.png'
		),
	);

	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);

	if (isset($_GET['action'])) // add, view, edit, delete
	{
		switch ($_GET['action'])
		{
			case 'view': // podgląd
			{
				$controller_object->View($id, $params, $access, $acl->available());
			}
			break;
		}
	}
	else // list
	{
		$controller_object->DrawList($params, $access, $acl->available());
	}

	$content_title = $controller_object->Get('content_title');
	$content_options = $controller_object->Get('content_options');

	$site_content = $view_object->ShowForm($model_object->GetParams(), $failed) . $controller_object->Get('site_content');

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
