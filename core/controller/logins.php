<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'logins');

$content_title = 'Logowania do serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Logins_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Logins_View($db);

$list_columns = array(
	array('db_name' => 'id', 			'column_name' => 'Id', 			'sorting' => 1),
	array('db_name' => 'agent', 		'column_name' => 'Agent',		'sorting' => 1),
	array('db_name' => 'user_ip', 		'column_name' => 'Adres IP',	'sorting' => 1),
	array('db_name' => 'login', 		'column_name' => 'Login', 		'sorting' => 1),
	array('db_name' => 'password', 		'column_name' => 'Hasło', 		'sorting' => 1),
	array('db_name' => 'user_id', 		'column_name' => 'Zalogowany', 	'sorting' => 1),
	array('db_name' => 'login_time',	'column_name' => 'Godzina', 	'sorting' => 1),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

$date_from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date("Y-m-d");
$date_to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date("Y-m-d");

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

$content_options = array (
	array (
		'address' => 'index.php?route=' . MODULE_NAME . '&mode=1',
		'caption' => 'Przyjęte',
		'icon' => 'img/accepted.png'
	),
	array (
		'address' => 'index.php?route=' . MODULE_NAME . '&mode=2',
		'caption' => 'Odrzucone',
		'icon' => 'img/rejected.png'
	),
	array (
		'address' => 'index.php?route=admin',
		'caption' => 'Zamknij',
		'icon' => 'img/stop.png'
	),
);

$params = array(
	'content_title' => $content_title,
	'content_options' => $content_options
);

$access = array(ADMIN);

$acl = new AccessControlList(MODULE_NAME, $db);
			
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
$site_content = $controller_object->Get('site_content');
$site_message = $controller_object->Get('site_message');
$site_dialog = $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
