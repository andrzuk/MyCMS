<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'layout');

$content_title = 'Układ serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Layout_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Layout_View($db);

$status = new Status($db);
$user_status = $status->get_value('user_status');

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = $page_options->get_options('simple');

// brakujące pola:
$failed = array();

$access = array(ADMIN, OPERATOR);

$acl = new AccessControlList(MODULE_NAME, $db);
			
if (in_array($user_status, $access) && $acl->available()) // są uprawnienia
{
	if (isset($_POST['save_button'])) // obsługa formularza
	{
		$contents = isset($_POST['contents']) ? trim($_POST['contents']) : NULL;
		
		// wymagane pola:
		$required = array(
			'contents' => $contents, 
		);

		foreach ($required as $k => $v)
			if (empty($v)) $failed[] = $k;
		
		// sprawdzanie czy uzupełniono wszystkie dane:
		if (empty($failed))
		{
			$record_object = array(
				'contents' => $contents, 
			);
			
			// zapisuje nową zawartość pliku CSS:
			$model_object->SaveContents($record_object);
			
			$site_dialog = array(
				'INFORMATION',
				'Zmiana układu',
				'Nowy układ strony został poprawnie zapisany.',
				array(
					array(
						'index.php?route=layout', 'Edytuj'
					),
					array(
						'index.php?route=admin', 'Zamknij', 'window.location.href=window.location.href'
					),
				)
			);
		}
		else // nie uzupełniono wszystkich pól
		{
			// wczytuje zawartość pliku CSS:	
			$contents = $model_object->GetContents();

			$record_object = array(
				'contents' => $contents, 
			);
		
			// wyświetla załadowany formularz:
			$site_content = $view_object->ShowForm($record_object, $failed);
			
			// wyświetla komunikat:
			$site_message = array(
				'WARNING', 'Zawartość nie może być pusta. Proszę uzupełnić.'
			);
		}
	}
	else if (isset($_POST['restore_button'])) // przywrócenie ustawień domyślnych
	{
		// kopiuje oryginalny plik CSS:
		$model_object->RestoreContents();
		
		$site_dialog = array(
			'INFORMATION',
			'Zmiana układu',
			'Domyślny układ strony został poprawnie przywrócony.',
			array(
				array(
					'index.php?route=layout', 'Edytuj'
				),
				array(
					'index.php?route=admin', 'Zamknij', 'window.location.href=window.location.href'
				),
			)
		);
	}
	else if (isset($_POST['cancel_button'])) // zamknięcie formularza
	{
			$site_dialog = array(
				'WARNING',
				'Zmiana układu',
				'Układ strony nie został zmieniony.',
				array(
					array(
						'index.php?route=admin', 'Zamknij'
					),
				)
			);
	}
	else // otwarcie do edycji
	{
		// wczytuje zawartość pliku CSS:	
		$contents = $model_object->GetContents();

		$record_object = array(
			'contents' => $contents, 
		);

		// wyświetla załadowany formularz:
		$site_content = $view_object->ShowForm($record_object, $failed);
	}
}
else // brak uprawnień
{
	$content_options = $page_options->get_options(NULL);

	$site_dialog = array(
		'ERROR',
		'Brak uprawnień',
		'Uruchomiona funkcja wymaga zalogowania do serwisu na konto o profilu administratora lub posiadania uprawnień określonych za pomocą systemu Access Control List.',
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
