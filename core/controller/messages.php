<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'messages');

$content_title = 'Wiadomości użytkowników';

$site_path = array (
	'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Messages_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Messages_View($db);

$list_columns = array(
	array('db_name' => 'id',              'column_name' => 'Id',            'sorting' => 1),
	array('db_name' => 'client_ip',       'column_name' => 'Adres IP',      'sorting' => 1),
	array('db_name' => 'client_name',     'column_name' => 'Imię lub nick', 'sorting' => 1),
	array('db_name' => 'client_email',    'column_name' => 'Adres e-mail',  'sorting' => 1),
	array('db_name' => 'message_content', 'column_name' => 'Wiadomość',     'sorting' => 1),
	array('db_name' => 'requested',       'column_name' => 'Zgłoszenie',    'sorting' => 1),
	array('db_name' => 'send_date',       'column_name' => 'Nadesłano',     'sorting' => 1),
	array('db_name' => 'close_date',      'column_name' => 'Zamknięto',     'sorting' => 1),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

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
		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'client_name', 
					'client_email',
					'message_content',
				),
				'check' => array(
					'client_name', 
					'client_email',
					'message_content',
				),
				'email' => 'client_email',
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

		// czyszczenie:
		
		case 'clear':
		{
			$content_options = $page_options->get_options('list');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->Clear($params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$content_options = array (
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=1',
			'caption' => 'Nadesłane',
			'icon' => 'img/report.png'
		),
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=2',
			'caption' => 'Zatwierdzone',
			'icon' => 'img/checked.png'
		),
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&action=clear',
			'caption' => 'Wyczyść',
			'icon' => 'img/trash.png'
		),
		array (
			'address' => 'index.php?route=admin',
			'caption' => 'Zamknij',
			'icon' => 'img/stop.png'
		),
	);
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options,
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
