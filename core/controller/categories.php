<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'categories');

$content_title = 'Kategorie serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Categories_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Categories_View($db);

$list_columns = array(
	array('db_name' => 'id', 				'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'type', 				'column_name' => 'Rodzaj', 			'sorting' => 1),
	array('db_name' => 'level', 			'column_name' => 'Poziom', 			'sorting' => 1),
	array('db_name' => 'parent_id', 		'column_name' => 'Rodzic', 			'sorting' => 1),
	array('db_name' => 'permission', 		'column_name' => 'Dostęp', 			'sorting' => 1),
	array('db_name' => 'item_order', 		'column_name' => 'Nr', 				'sorting' => 1),
	array('db_name' => 'caption', 			'column_name' => 'Tytuł (tekst)', 	'sorting' => 1),
	array('db_name' => 'link', 				'column_name' => 'Adres (link)', 	'sorting' => 1),
	array('db_name' => 'icon_id', 			'column_name' => 'Grafika', 		'sorting' => 1),
	array('db_name' => 'page_id', 			'column_name' => 'Strona',			'sorting' => 1),
	array('db_name' => 'visible', 			'column_name' => 'Widoczna', 		'sorting' => 1),
	array('db_name' => 'target', 			'column_name' => 'Nowe okno', 		'sorting' => 1),
	array('db_name' => 'modified', 			'column_name' => 'Modyfikacja', 	'sorting' => 1),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

$page_id = isset($_GET['id']) ? $model_object->GetPageId($id) : NULL;

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array(
	'parent' => $model_object->GetParents(),
	'order' => $model_object->GetOrders(),
);

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
					'caption', 
					'link',
				),
				'check' => array(
					'caption',
					'link',
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->Add($params, $access, $acl->available());
		}
		break;

		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');
			
			if ($page_id)
			{
				$additional_options = array (
					'address' => 'index.php?route=pages&action=edit&id=' . $page_id,
					'caption' => 'Edytuj stronę',
					'icon' => 'img/category.png'
				);
				$content_options[] = $additional_options;
			}

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'caption', 
					'link',
				),
				'check' => array(
					'caption',
					'link',
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
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
			
			$access = array(ADMIN, OPERATOR);
			
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
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Delete($id, $params, $access, $acl->available());
		}
		break;

		// przesuwanie w górę:
		
		case 'move-up':
		{
			$content_options = $page_options->get_options('list');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->MoveUp($id, $params, $access, $acl->available());
		}
		break;
		
		// przesuwanie w dół:
		
		case 'move-down':
		{
			$content_options = $page_options->get_options('list');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->MoveDown($id, $params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$list_options = $page_options->get_options('list');
	
	$mode_options = array (
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=1',
			'caption' => 'Pasek nawigacji',
			'icon' => 'img/top_menu.png'
		),
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=2',
			'caption' => 'Menu boczne',
			'icon' => 'img/left_menu.png'
		),
	);

	$content_options = array_merge($list_options, $mode_options);
		
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);
	
	$access = array(ADMIN, OPERATOR);
	
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
