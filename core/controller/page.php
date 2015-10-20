<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'page');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Page_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Page_View($db);

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

// dane z bazy potrzebne na stronę:

$data_import = array(
	'authors' => $model_object->GetAuthors(),
);

$status = new Status($db);
$user_status = $status->get_value('user_status');

// ustala permission taki jak dla powiązanej kategorii:

$record_object = $model_object->GetCategory($id);

if ($record_object) // kategoria istnieje
{
	$permission = $record_object['permission'];
	$visible = $record_object['visible'];

	$access = $user_status ? $user_status <= $permission : $permission == FREE;
	$access &= $visible;	
}
else // strona bez kategorii
{
	$access = TRUE;
}

if ($access) // są uprawnienia
{
	// pobiera rekord o podanym id:
	$record_object = $model_object->GetPageContent($id);

	if ($record_object) // strona istnieje
	{
		// wyświetla tytuł strony:
		$content_title = $view_object->ShowTitle($record_object, $data_import);

		// wyświetla zawartość strony:
		$site_content = $view_object->ShowPage($record_object);
	}
	else // strona nie istnieje
	{
		$site_dialog = array(
			'ERROR',
			'Błąd podstrony',
			'Strona nie została znaleziona.'.
			'<br />Sprawdź, czy podany w adresie identyfikator strony jest prawidłowy.',
			array(
				array(
					'index.php', 'Zamknij'
				)
			)
		);
	}
}
else // brak uprawnień
{
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

$content_title = !empty($content_title) ? $content_title : 'Strona nie znaleziona';

$site_content = !empty($site_content) ? $site_content : NULL;

// ścieżka strony:
$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=' . MODULE_NAME . '&id=' . $id => $model_object->GetTitle($id),
);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
