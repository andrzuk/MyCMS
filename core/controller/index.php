<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'index');

$content_title = 'Strona główna';

$site_path = array (
    'index.php' => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Index_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Index_View($db);

if (isset($installation)) // folder "install" istnieje (etap instalacji)
{
	if ($model_object->IsInstalled()) // skrypty sql zainstalowane
	{
		$site_dialog = array(
			'WARNING',
			'Instalacja',
			'Instalacja serwisu nie została zakończona. Usuń z serwisu katalog "<b>install</b>".',
			array(
				array(
					'index.php', 'Zamknij'
				),
			)
		);
	}
	else // skrypty sql nie zainstalowane
	{
		$site_dialog = array(
			'WARNING',
			'Instalacja',
			'Serwis nie został zainstalowany. Wprowadź ustawienia konfiguracyjne serwisu.',
			array(
				array(
					'install/index.php', 'Instaluj'
				),
			)
		);
	}
}
else // folder "install" nie istnieje (etap eksploatacji)
{
	// dane z bazy potrzebne na stronę:

	$data_import = array(
		'authors' => $model_object->GetAuthors(),
	);

	// pobiera rekord z atrybutem main_page:
	$record_object = $model_object->GetPageContent();

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
			'Błąd strony',
			'Strona nie została znaleziona.'.
			'<br />Strona główna nie istnieje lub jest pusta.',
			array(
				array(
					'index.php', 'Zamknij'
				)
			)
		);
	}	
}

$content_title = !empty($content_title) ? $content_title : 'Strona nie znaleziona';

$site_content = !empty($site_content) && empty($site_dialog) ? $site_content : NULL;

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
