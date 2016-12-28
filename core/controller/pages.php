<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'pages');

$content_title = 'Strony serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Pages_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Pages_View($db);

$status = new Status($db);
$user_id = $status->get_value('user_id');

// pola 'db_name' muszą być zgodne co do nazwy i kolejności
// z polami zwracanymi przez Model w metodzie GetAll oraz używanymi w metodach Add i Edit

$list_columns = array(
	array('db_name' => 'id', 				'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'main_page', 		'column_name' => NULL,	 			'sorting' => 0),
	array('db_name' => 'system_page', 		'column_name' => NULL,	 			'sorting' => 0),
	array('db_name' => 'title', 			'column_name' => 'Tytuł', 			'sorting' => 1),
	array('db_name' => 'contents', 			'column_name' => 'Treść',	 		'sorting' => 1),
	array('db_name' => 'category_id', 		'column_name' => NULL,	 			'sorting' => 0),
	array('db_name' => 'caption',			'column_name' => 'Kategoria', 		'sorting' => 1),
	array('db_name' => 'author_id', 		'column_name' => NULL,	 			'sorting' => 0),
	array('db_name' => 'user_login',		'column_name' => 'Autor', 			'sorting' => 1),
	array('db_name' => 'modified', 			'column_name' => 'Modyfikacja', 	'sorting' => 1),
	array('db_name' => 'visible', 			'column_name' => 'Widoczna',	 	'sorting' => 0),
);

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

$category_id = isset($_GET['id']) ? $model_object->GetCategoryId($id) : NULL;

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array(
	'author' => $user_id,
	'authors' => $model_object->GetAuthors(),
	'category' => $model_object->GetCategories(),
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
					'title', 
					'category_id',
					'contents',
				),
				'check' => array(
					'title',
					'contents',
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

			$additional_options = array (
				array (
					'address' => 'index.php?route=categories&action=edit&id=' . $category_id,
					'caption' => 'Edytuj kategorię',
					'icon' => 'img/category.png'
				),
			);
			
			$content_options = array_merge($additional_options, $content_options);

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'title',
					'category_id',
					'contents',
				),
				'check' => array(
					'title',
					'contents',
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

		// archiwizacja:
		
		case 'archive':
		{
			$content_options = $page_options->get_options('list');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->Archive($id, $params, $access, $acl->available());
		}
		break;

		// przywracanie:
		
		case 'restore':
		{
			$content_options = $page_options->get_options('list');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->Restore($id, $params, $access, $acl->available());
		}
		break;

		// podgląd wersji:
		
		case 'preview':
		{
			$content_options = $page_options->get_options('add');
			
			$params = array(
				'content_title' => $content_title . ' - Podgląd',
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->ShowPreview($id, $params, $access, $acl->available());
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
