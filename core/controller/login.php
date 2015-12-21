<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'login');

$content_title = 'Logowanie do serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Login_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Login_View($db);

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

if (!isset($_SESSION['user_status']) || !$_SESSION['user_status']) // nie zalogowany
{
	if (isset($_POST['login_button'])) // obsługa formularza
	{
		if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
		{
			$_SESSION['form_sent'] = $_POST['form_hash'];

			$login = isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : NULL;
			$password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : NULL;
			
			// resetuje ustawienia uzytkownika:
			$_SESSION['user_id'] = 0;
			$_SESSION['user_status'] = 0;
			$_SESSION['user_login'] = NULL;
			$_SESSION['user_name'] = NULL;

			// zapamiętuje dane w formularzu:
			$_SESSION['form_fields']['login'] = $login;
			$_SESSION['form_fields']['password'] = $password;

			// wymagane pola:
			$required = array(
				'login' => $login, 
				'password' => $password
			);

			foreach ($required as $k => $v)
				if (empty($v)) $failed[] = $k;
			
			// sprawdzanie czy uzupełniono wszystkie dane:
			if (empty($failed))
			{
				$record_object = array(
					'user_login' => $login, 
					'user_password' => $password
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
					
					$result = $model_object->Login($record_object);
					
					if ($result) // logowanie się powiodło
					{
						// ustawia użytkownika:
						$_SESSION['user_id'] = $result['id'];
						$_SESSION['user_status'] = $result['status'];
						$_SESSION['user_imie'] = $result['imie'];
						$_SESSION['user_nazwisko'] = $result['nazwisko'];
						$_SESSION['user_login'] = $result['user_login'];
						$_SESSION['user_email'] = $result['email'];
						
						$site_dialog = array(
							'INFORMATION',
							'Logowanie',
							'Zostałeś poprawnie zalogowany do serwisu.',
							array(
								array(
									'index.php?route=admin', 'Panel'
								),
								array(
									'index.php?route=profile', 'Profil'
								),
							)
						);
					}
					else // logowanie się nie powiodło
					{
						// wyświetla pusty formularz:
						$site_content = $view_object->ShowForm(NULL, $failed);
						
						// wyświetla komunikat:
						$site_message = array(
							'ERROR', 'Nieprawidłowy login lub e-mail lub hasło lub też konto zostało zablokowane.'
						);
					}
					
					$login_object = array('server' => $_SERVER, 'session' => $_SESSION);
					
					// rejestruje próbę logowania:
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
					'WARNING', 'Proszę wpisać login lub e-mail oraz hasło.'
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
		$_SESSION['form_fields']['password'] = NULL;
		
		// wyświetla pusty formularz:
		$site_content = $view_object->ShowForm(NULL, $failed);
	}
}
else // zalogowany
{
	$site_dialog = array(
		'WARNING',
		'Logowanie',
		'Zostałeś już zalogowany do serwisu.',
		array(
			array(
				'index.php?route=logout', 'Wyloguj'
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
