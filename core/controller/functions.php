<?php

define ('MODULE_NAME', 'functions');

$content_title = 'Funkcje serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Functions_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Functions_View($db);

$list_columns = array(
	array('db_name' => 'id', 			'column_name' => 'Id', 			'sorting' => 1),
	array('db_name' => 'function', 		'column_name' => 'Funkcja',		'sorting' => 1),
	array('db_name' => 'meaning', 		'column_name' => 'Znaczenie',	'sorting' => 1),
	array('db_name' => 'module', 		'column_name' => 'Moduł',		'sorting' => 1),
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

$page_options = new Options(MODULE_NAME, $id);

$access = array(ADMIN);

$acl = new AccessControlList(MODULE_NAME, $db);
			
if (isset($_GET['action'])) // add, view, edit, delete
{
	switch ($_GET['action'])
	{
		// dodawanie:
		
		case 'add':
		{
			$content_options = $page_options->get_options('add');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'function', 
					'meaning',
					'module',
				),
				'check' => array(
					'function', 
					'meaning',
					'module',
				),
			);
			
			$controller_object->Add($params, $access, $acl->available());
		}
		break;

		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'function', 
					'meaning',
					'module',
				),
				'check' => array(
					'function', 
					'meaning',
					'module',
				),
			);
			
			$controller_object->Edit($id, $params, $access, $acl->available());
		}
		break;

		// podgląd:
		
		case 'view':
		{
			$content_options = $page_options->get_options('view');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$controller_object->View($id, $params, $access, $acl->available());
		}
		break;

		// usuwanie:
		
		case 'delete':
		{
			$content_options = $page_options->get_options('delete');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$controller_object->Delete($id, $params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$list_options = $page_options->get_options('list');

	$function_options = array (
		array (
			'address' => 'index.php?route=roles',
			'caption' => 'Role użytkowników',
			'icon' => 'img/access.png'
		),
	);

	$content_options = array_merge($list_options, $function_options);
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);
	
	$controller_object->DrawList($params, $access, $acl->available());
}
			
$content_title = $controller_object->Get('content_title');
$content_options = $controller_object->Get('content_options');
$site_content = $controller_object->Get('site_content');
$site_message = $controller_object->Get('site_message');
$site_dialog = $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
