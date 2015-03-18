<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'password');

$content_title = 'Hasło do serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Password_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Password_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = $page_options->get_options('login');

// brakujące pola:
$failed = array();

if (isset($_POST['send_button'])) // obsługa formularza
{
	if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
	{
		$_SESSION['form_sent'] = $_POST['form_hash'];

		$login = isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : NULL;
		$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : NULL;
		
		// zapamiętuje dane w formularzu:
		$_SESSION['form_fields']['login'] = $login;
		$_SESSION['form_fields']['email'] = $email;

		// wymagane pola:
		$required = array(
			'login' => $login, 
			'email' => $email
		);

		foreach ($required as $k => $v)
			if (empty($v)) $failed[] = $k;
		
		// sprawdzanie czy uzupełniono wszystkie dane:
		if (empty($failed))
		{
			$record_object = array(
				'login' => $login, 
				'email' => $email
			);
			
			$input_check = NULL;
			foreach ($record_object as $k => $v) 
				$input_check .= $v .' ';

			include LIB_DIR . 'validator.php';
			
			$validator_object = new Validator();
			
			$check_result = $validator_object->check_security($input_check);
			
			if ($check_result) // kontrola bezpieczeństwa poprawna
			{
				// weryfikuje użytkownika na podstawie bazy:
				
				$result = $model_object->Reset($record_object);
				
				if ($result) // reset hasła się powiódł (login i email się zgadzają)
				{
					$site_dialog = array(
						'INFORMATION',
						'Reset hasła',
						'Hasło zostało poprawnie zresetowane. Nowe hasło zostało wysłane drogą e-mailową.',
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
				else // reset hasła się nie powiódł (login i email się nie zgadzają)
				{
					// wyświetla pusty formularz:
					$site_content = $view_object->ShowForm(NULL, $failed);
					
					// wyświetla komunikat:
					$site_message = array(
						'ERROR', 'Nieprawidłowy login lub e-mail lub też konto zostało zablokowane.'
					);
				}
				
				$login_object = array('server' => $_SERVER, 'session' => $_SESSION, 'result' => $result);
				
				// rejestruje próbę odzyskania hasła:
				$model_object->Store($record_object, $login_object);
			}
			else // nie przeszło kontroli bezpieczeństwa
			{
				// wyświetla pusty formularz:
				$site_content = $view_object->ShowForm(NULL, $failed);
				
				// wyświetla komunikat:
				$site_message = array(
					'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
				);
			}
		}
		else // nie uzupełniono wszystkich pól
		{
			// wyświetla pusty formularz:
			$site_content = $view_object->ShowForm(NULL, $failed);
			
			// wyświetla komunikat:
			$site_message = array(
				'WARNING', 'Proszę wpisać login oraz e-mail podany podczas rejestracji.'
			);
		}
	}
	else // odświeżono formularz
	{
		$content_options = $page_options->get_options(NULL);

		$site_dialog = array(
			'WARNING',
			'Niedozwolona operacja',
			'Formularz został już wysłany i nie należy go odświeżać.',
			array(
				array(
					'index.php', 'Zamknij'
				),
			)
		);
	}
}
else // pusty formularz
{
	// czyści dane w formularzu:
	$_SESSION['form_fields']['login'] = NULL;
	$_SESSION['form_fields']['email'] = NULL;
	
	// wyświetla pusty formularz:
	$site_content = $view_object->ShowForm(NULL, $failed);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
