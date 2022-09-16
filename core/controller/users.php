<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'users');

$content_title = 'Użytkownicy serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Users_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Users_View($db);

$status = new Status($db);
$user_id = $status->get_value('user_id');
$user_status = $status->get_value('user_status');

// ograniczenia dla usera - prawa tylko do własnego id
// dla grupy (1, 2) czyli (admin, operator) brak ograniczeń:

if ($user_id) $restrict = in_array($user_status, array(ADMIN, OPERATOR)) ? NULL : $user_id;
else $restrict = -1;

$list_columns = array(
	array('db_name' => 'id', 				'column_name' => 'Id', 			'sorting' => 1),
	array('db_name' => 'user_login', 		'column_name' => 'Login',		'sorting' => 1),
	array('db_name' => 'user_password', 	'column_name' => 'Hasło',		'sorting' => 1),
	array('db_name' => 'imie', 				'column_name' => 'Imię', 		'sorting' => 1),
	array('db_name' => 'nazwisko', 			'column_name' => 'Nazwisko', 	'sorting' => 1),
	array('db_name' => 'email', 			'column_name' => 'E-mail', 		'sorting' => 1),
	array('db_name' => 'status', 			'column_name' => 'Grupa', 		'sorting' => 1),
	array('db_name' => 'ulica', 			'column_name' => 'Ulica', 		'sorting' => 1),
	array('db_name' => 'kod', 				'column_name' => 'Kod', 		'sorting' => 1),
	array('db_name' => 'miasto', 			'column_name' => 'Miasto', 		'sorting' => 1),
	array('db_name' => 'pesel', 			'column_name' => 'Pesel', 		'sorting' => 1),
	array('db_name' => 'telefon', 			'column_name' => 'Telefon', 	'sorting' => 1),
	array('db_name' => 'data_rejestracji', 	'column_name' => 'Rejestracja', 'sorting' => 1),
	array('db_name' => 'data_logowania', 	'column_name' => 'Logowanie', 	'sorting' => 1),
	array('db_name' => 'data_modyfikacji', 	'column_name' => 'Modyfikacja', 'sorting' => 1),
	array('db_name' => 'data_wylogowania', 	'column_name' => 'Wylogowanie',	'sorting' => 1),
	array('db_name' => 'active', 			'column_name' => 'Aktywny', 	'sorting' => 1),
);

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$navi_object->set_restrict($restrict);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array(
	'user_status' => $user_status,
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
			
			if (isset($_POST['update_button']) || isset($_POST['cancel_button'])) 
			{
				$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('add');
			}

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'user_login', 
					'email',
					'imie',
					'nazwisko',
				),
				'check' => array(
					'user_login', 
					'imie',
					'nazwisko',
					'email',
					'ulica',
					'kod',
					'miasto',
					'pesel',
					'telefon',
				),
				'email' => 'email',
				'pesel' => 'pesel',
				'restrict' => $restrict,
				'unique' => array(
					'user_login', 
					'email',
					'pesel',
				),
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->Add($params, $access, $acl->available());
		}
		break;

		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');

			if (isset($_POST['update_button']) || isset($_POST['cancel_button'])) 
			{
				$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('edit');
			}

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'user_login', 
					'email',
					'imie',
					'nazwisko',
				),
				'check' => array(
					'user_login', 
					'imie',
					'nazwisko',
					'email',
					'ulica',
					'kod',
					'miasto',
					'pesel',
					'telefon',
				),
				'email' => 'email',
				'pesel' => 'pesel',
				'restrict' => $restrict,
				'unique' => array(
					'user_login', 
					'email',
					'pesel',
				),
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			if ($model_object->AllowProfile($id)) // są uprawnienia
			{
				$controller_object->Edit($id, $params, $access, $acl->available());
			}
			else // brak uprawnień
			{
				$site_dialog = array(
					'ERROR',
					'Brak uprawnień',
					'Uruchomiona akcja wymaga posiadania uprawnień co najmniej takich samych jak profil wybranej pozycji.',
					array(
						array(
							'index.php?route=users', 'Zamknij'
						),
					)
				);
			}
		}
		break;

		// podgląd:
		
		case 'view':
		{
			$content_options = $page_options->get_options('view');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'restrict' => $restrict,
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
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
				'content_options' => $content_options,
				'restrict' => $restrict,
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			if ($model_object->AllowProfile($id)) // są uprawnienia
			{
				$controller_object->Delete($id, $params, $access, $acl->available());
			}
			else // brak uprawnień
			{
				$site_dialog = array(
					'ERROR',
					'Brak uprawnień',
					'Uruchomiona akcja wymaga posiadania uprawnień co najmniej takich samych jak profil wybranej pozycji.',
					array(
						array(
							'index.php?route=users', 'Zamknij'
						),
					)
				);
			}
		}
		break;
	}
}
else // list of all
{
	$content_options = $page_options->get_options('list');
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options,
		'restrict' => $restrict,
	);
	
	$access = array(ADMIN, OPERATOR, USER);
	
	$acl = new AccessControlList(MODULE_NAME, $db);
			
	$controller_object->DrawList($params, $access, $acl->available());
}
			
$content_title = isset($content_title) ? $content_title : $controller_object->Get('content_title');
$content_options = isset($content_options) ? $content_options : $controller_object->Get('content_options');
$site_content = isset($site_content) ? $site_content : $controller_object->Get('site_content');
$site_message = isset($site_message) ? $site_message : $controller_object->Get('site_message');
$site_dialog = isset($site_dialog) ? $site_dialog : $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
