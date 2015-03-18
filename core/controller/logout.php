<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'logout');

$content_title = 'Wylogowanie z serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Logout_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Logout_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = $page_options->get_options('logout');

$status = new Status($db);
$user_id = $status->get_value('user_id');

// dodatkowe czynności na bazie:

$result = $model_object->Logout($user_id);

if ($result) // wylogowanie się powiodło
{
	// resetuje ustawienia uzytkownika:

	$_SESSION['user_id'] = 0;
	$_SESSION['user_status'] = 0;
	$_SESSION['user_login'] = NULL;
	$_SESSION['user_name'] = NULL;
	$_SESSION['user_imie'] = NULL;
	$_SESSION['user_nazwisko'] = NULL;
	$_SESSION['user_email'] = NULL;
	$_SESSION['form_fields'] = NULL;
	$_SESSION['mode'] = NULL;
	$_SESSION['list_filter'] = NULL;

	unset($_SESSION['user_id']);
	unset($_SESSION['user_status']);
	unset($_SESSION['user_login']);
	unset($_SESSION['user_name']);
	unset($_SESSION['user_imie']);
	unset($_SESSION['user_nazwisko']);
	unset($_SESSION['user_email']);
	unset($_SESSION['form_fields']);
	unset($_SESSION['mode']);
	unset($_SESSION['list_filter']);
	
	// kasuje sesję:

	session_destroy();
	
	$site_dialog = array(
		'INFORMATION',
		'Wylogowanie',
		'Zostałeś poprawnie wylogowany z serwisu.',
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
else // wylogowanie się nie powiodło
{
	$site_dialog = array(
		'WARNING',
		'Wylogowanie',
		'Zostałeś już wylogowany z serwisu.',
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
