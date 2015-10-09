<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'category');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Category_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Category_View($db);

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

$current_category = $id;

$status = new Status($db);
$user_status = $status->get_value('user_status');

// ustala permission dla kategorii:

$record_object = $model_object->GetCategory($id);

if ($record_object) // kategoria istnieje
{
	$permission = $record_object['permission'];

	$access = $user_status ? $user_status <= $permission : $permission == FREE;

	if ($access) // są uprawnienia
	{
		// dane z bazy potrzebne na stronę:
		$data_import = array(
			'authors' => $model_object->GetAuthors(),
		);

		// pobiera rekord wyznaczony z id kategorii:
		$record_object = $model_object->GetPageContent($id);

		// wyświetla tytuł strony:
		$content_title = $view_object->ShowTitle($record_object, $data_import);

		// wyświetla zawartość strony:
		$site_content = $view_object->ShowPage($record_object);
		
		if (empty($site_content)) // brak strony powiązanej z kategorią
		{
			$site_dialog = array(
				'ERROR',
				'Błąd podstrony',
				'Strona nie została znaleziona.'.
				'<br />Strona powiązana z bieżącą kategorią nie istnieje lub jest pusta.',
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
}
else // kategoria nie istnieje
{
	$site_dialog = array(
		'ERROR',
		'Błąd podstrony',
		'Strona nie została znaleziona.'.
		'<br />Sprawdź, czy podany w adresie identyfikator kategorii jest prawidłowy.',
		array(
			array(
				'index.php', 'Zamknij'
			)
		)
	);
}

$content_title = !empty($content_title) ? $content_title : 'Strona nie znaleziona';

$site_content = !empty($site_content) ? $site_content : NULL;

// ścieżka strony:
$site_path = $model_object->GetPath($id);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
