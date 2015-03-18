<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'register');

$content_title = 'Rejestracja w serwisie';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Register_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Register_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = $page_options->get_options('register');

// brakujące pola:
$failed = array();

if (isset($_POST['register_button'])) // obsługa formularza
{
	if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
	{
		$_SESSION['form_sent'] = $_POST['form_hash'];

		$imie = isset($_POST['imie']) ? htmlspecialchars(trim($_POST['imie'])) : NULL;
		$nazwisko = isset($_POST['nazwisko']) ? htmlspecialchars(trim($_POST['nazwisko'])) : NULL;
		$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : NULL;
		$login = isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : NULL;
		$password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : NULL;
		
		// resetuje ustawienia uzytkownika:
		$_SESSION['user_id'] = 0;
		$_SESSION['user_status'] = 0;
		$_SESSION['user_login'] = NULL;
		$_SESSION['user_name'] = NULL;

		// zapamiętuje dane w formularzu:
		$_SESSION['form_fields']['imie'] = $imie;
		$_SESSION['form_fields']['nazwisko'] = $nazwisko;
		$_SESSION['form_fields']['email'] = $email;
		$_SESSION['form_fields']['login'] = $login;
		$_SESSION['form_fields']['password'] = $password;

		// wymagane pola:
		$required = array(
			'imie' => $imie, 
			'nazwisko' => $nazwisko, 
			'email' => $email, 
			'login' => $login, 
			'password' => $password,
		);

		foreach ($required as $k => $v)
			if (empty($v)) $failed[] = $k;
		
		// sprawdzanie czy uzupełniono wszystkie dane:
		if (empty($failed))
		{
			$record_object = array(
				'imie' => $imie, 
				'nazwisko' => $nazwisko, 
				'email' => $email, 
				'user_login' => $login, 
				'user_password' => $password,
			);

			$input_check = NULL;
			foreach ($record_object as $k => $v) 
				$input_check .= $v .' ';

			include LIB_DIR . 'validator.php';
			
			$validator_object = new Validator();
			
			$check_result = $validator_object->check_security($input_check);
			
			$check_email = $validator_object->check_email($email);
			
			$check_exist = $model_object->Exist($record_object);
			
			if ($check_result) // kontrola bezpieczeństwa poprawna
			{
				if ($check_email) // email poprawny
				{
					if (!$check_exist) // nie istnieje jeszcze taki login ani email
					{
						// zapisuje użytkownika do bazy:
						
						$result = $model_object->Register($record_object);
						
						if ($result) // zapis się powiódł
						{
							// ustawia użytkownika:
							$_SESSION['user_id'] = $result['id'];
							$_SESSION['user_status'] = $result['status'];
							$_SESSION['user_imie'] = $result['imie'];
							$_SESSION['user_nazwisko'] = $result['nazwisko'];
							$_SESSION['user_login'] = $result['user_login'];
							$_SESSION['user_email'] = $result['email'];
							
							// pokazuje user-details:
							
							$page_options = new Options('users', $result['id']);

							$content_options = $page_options->get_options('view');

							$list_columns = array(
								array('db_name' => 'user_login', 		'column_name' => 'Login',		'color' => '#900'),
								array('db_name' => 'imie', 				'column_name' => 'Imię', 		'color' => '#369'),
								array('db_name' => 'nazwisko', 			'column_name' => 'Nazwisko', 	'color' => '#369'),
								array('db_name' => 'email', 			'column_name' => 'E-mail', 		'color' => '#69c'),
								array('db_name' => 'data_rejestracji', 	'column_name' => 'Rejestracja', 'color' => '#090'),
								array('db_name' => 'data_logowania', 	'column_name' => 'Logowanie', 	'color' => '#369'),
								array('db_name' => 'data_modyfikacji', 	'column_name' => 'Modyfikacja', 'color' => '#036'),
								array('db_name' => 'data_wylogowania', 	'column_name' => 'Wylogowanie',	'color' => '#d00'),
							);

							// pobiera rekord o danym Id:
							$login_object = $model_object->GetOne($result['id']);
							
							// wyświetla formularz wypełniony danymi:
							$site_content = $view_object->ShowDetails($login_object, $list_columns);

							// user-details.

							// wyświetla komunikat:
							$site_message = array(
								'INFORMATION', 'Zostałeś poprawnie zarejestrowany w serwisie.'
							);
						}
						else // rejestracja się nie powiodła
						{
							// wyświetla pusty formularz:
							$site_content = $view_object->ShowForm(NULL, $failed);
							
							// wyświetla komunikat:
							$site_message = array(
								'ERROR', 'Rejestracja zakończyła się niepowodzeniem.'
							);
						}
					}
					else // już istnieje taki login lub email
					{
						// wyświetla pusty formularz:
						$site_content = $view_object->ShowForm(NULL, $failed);
						
						// wyświetla komunikat:
						$site_message = array(
							'ERROR', 'Użytkownik o takim loginie lub adresie e-mail już istnieje.'
						);
					}
				}
				else // email niepoprawny
				{
					// oznacza niepoprawne pole:
					$failed[] = 'email';
					
					// wyświetla pusty formularz:
					$site_content = $view_object->ShowForm(NULL, $failed);
					
					// wyświetla komunikat:
					$site_message = array(
						'ERROR', 'Nieprawidłowy adres e-mail. Proszę poprawić.'
					);
				}
				
				$result = isset($result) ? $result : NULL;
				$login_object = array('server' => $_SERVER, 'session' => $_SESSION, 'result' => $result);
				
				// rejestruje próbę rejestracji:
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
				'WARNING', 'Nie wypełniono wszystkich wymaganych pól. Proszę uzupełnić.'
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
	$_SESSION['form_fields']['imie'] = NULL;
	$_SESSION['form_fields']['nazwisko'] = NULL;
	$_SESSION['form_fields']['email'] = NULL;
	$_SESSION['form_fields']['login'] = NULL;
	$_SESSION['form_fields']['password'] = NULL;
	
	// wyświetla pusty formularz:
	$site_content = $view_object->ShowForm(NULL, $failed);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
