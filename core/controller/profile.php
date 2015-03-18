<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'profile');

$content_title = 'Profil użytkownika';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Profile_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Profile_View($db);

$status = new Status($db);
$user_status = $status->get_value('user_status');
$user_id = $status->get_value('user_id');

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options('users', $user_id);

$access = array(ADMIN, OPERATOR, USER);

if (in_array($user_status, $access)) // są uprawnienia
{
	// pokazuje user-details:

	$content_options = $page_options->get_options('view');

	$list_columns = array(
		array('db_name' => 'id', 				'column_name' => 'Id',			'color' => '#000'),
		array('db_name' => 'user_login', 		'column_name' => 'Login',		'color' => '#900'),
		array('db_name' => 'imie', 				'column_name' => 'Imię', 		'color' => '#369'),
		array('db_name' => 'nazwisko', 			'column_name' => 'Nazwisko', 	'color' => '#369'),
		array('db_name' => 'email', 			'column_name' => 'E-mail', 		'color' => '#69c'),
		array('db_name' => 'status', 			'column_name' => 'Grupa', 		'color' => '#900'),
		array('db_name' => 'data_rejestracji', 	'column_name' => 'Rejestracja', 'color' => '#090'),
		array('db_name' => 'data_logowania', 	'column_name' => 'Logowanie', 	'color' => '#369'),
		array('db_name' => 'data_modyfikacji', 	'column_name' => 'Modyfikacja', 'color' => '#036'),
		array('db_name' => 'data_wylogowania', 	'column_name' => 'Wylogowanie',	'color' => '#d00'),
	);

	// pobiera rekord o danym Id:
	$record_object = $model_object->GetDetails($user_id);

	// wyświetla formularz wypełniony danymi:
	$site_content = $view_object->ShowDetails($record_object, $list_columns);
}
else // brak uprawnień
{
	$content_options = $page_options->get_options(NULL);

	$site_dialog = array(
		'ERROR',
		'Brak uprawnień',
		'Uruchomiona funkcja wymaga zalogowania do serwisu na konto o profilu administratora.',
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
