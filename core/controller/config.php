<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'config');

$content_title = 'Konfiguracja serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Config_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Config_View($db);

$list_columns = array(
	array('db_name' => 'id', 				'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'key_name', 			'column_name' => 'Nazwa klucza',	'sorting' => 1),
	array('db_name' => 'key_value', 		'column_name' => 'Wartość klucza',	'sorting' => 1),
	array('db_name' => 'meaning', 			'column_name' => 'Znaczenie', 		'sorting' => 1),
	array('db_name' => 'field_type', 		'column_name' => 'Typ', 			'sorting' => 1),
	array('db_name' => 'active', 			'column_name' => 'Aktywny', 		'sorting' => 1),
	array('db_name' => 'modified', 			'column_name' => 'Modyfikacja', 	'sorting' => 1),
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
					'key_name', 
					'key_value',
					'meaning',
				),
				'check' => array(
					'key_name', 
					'key_value',
					'meaning',
				),
			);
			
			$access = array(ADMIN);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

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
					'key_name', 
					'key_value',
					'meaning',
				),
				'check' => array(
					'key_name', 
					'key_value',
					'meaning',
				),
			);
			
			$access = array(ADMIN);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

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
			
			$access = array(ADMIN);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

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
			
			$access = array(ADMIN);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Delete($id, $params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$content_options = $page_options->get_options('list');
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);
	
	$access = array(ADMIN);
	
	$acl = new AccessControlList(MODULE_NAME, $db);

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
