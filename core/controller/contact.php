<?php

/*
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'contact');

$content_title = 'Kontakt z serwisem';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Contact_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Contact_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

// opcje dla podstrony:

$content_options = array();

// pobiera rekord z atrybutem contact:
$record_object = $model_object->GetPageContent();

// brakujące pola:
$failed = array();

if (isset($_POST['send_button'])) // obsługa formularza
{
	if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
	{
		$_SESSION['form_sent'] = $_POST['form_hash'];

		$autor = isset($_POST['autor']) ? htmlspecialchars(trim($_POST['autor'])) : NULL;
		$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : NULL;
		$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : NULL;
		$send_copy = isset($_POST["send_copy"]) ? $_POST["send_copy"] : NULL;
		
		// zapamiętuje dane w formularzu:
		$_SESSION['form_fields']['autor'] = $autor;
		$_SESSION['form_fields']['email'] = $email;
		$_SESSION['form_fields']['message'] = $message;

		// wymagane pola:
		$required = array(
			'autor' => $autor, 
			'email' => $email, 
			'message' => $message,
		);

		foreach ($required as $k => $v)
			if (empty($v)) $failed[] = $k;
		
		// sprawdzanie czy uzupełniono wszystkie dane:
		if (empty($failed))
		{
			$form_object = array(
				'autor' => $autor, 
				'email' => $email,
				'message' => $message,
			);
			
			$input_check = NULL;
			foreach ($form_object as $k => $v) 
				$input_check .= $v .' ';

			include LIB_DIR . 'validator.php';
			
			$validator_object = new Validator();
			
			$check_result = $validator_object->check_security($input_check);
			
			$email_result = $validator_object->check_email($email);

			if ($check_result) // kontrola bezpieczeństwa poprawna
			{
				if ($email_result) // email poprawny
				{
					$send_object = array('server' => $_SERVER, 'session' => $_SESSION);

					// rejestruje wiadomość użytkownika:
					
					$result = $model_object->Receive($form_object, $send_object, $send_copy);
					
					if ($result) // zapis do bazy poprawny
					{
						$site_dialog = array(
							'INFORMATION',
							'Kontakt z serwisem',
							'Dziękujemy! Twoja wiadomość została wysłana do serwisu. Ustosunkujemy się do niej niezwłocznie.',
							array(
								array(
									'index.php?route=contact', 'Kontakt'
								),
								array(
									'index.php', 'Zamknij'
								),
							)
						);
					}
					else // zapis do bazy nieudany
					{
						// wyświetla pusty formularz:
						$site_content = $view_object->ShowForm($record_object, $failed);
						
						// wyświetla komunikat:
						$site_message = array(
							'ERROR', 'Nieudana rejestracja wiadomości. Proszę spróbować ponownie.'
						);
					}
				}
				else // email niepoprawny
				{
					// oznacza niepoprawne pole:
					$failed[] = 'email';
					
					// wyświetla pusty formularz:
					$site_content = $view_object->ShowForm($record_object, $failed);
					
					// wyświetla komunikat:
					$site_message = array(
						'ERROR', 'Nieprawidłowy adres e-mail. Proszę poprawić.'
					);
				}
			}
			else // nie przeszło kontroli bezpieczeństwa
			{
				// wyświetla pusty formularz:
				$site_content = $view_object->ShowForm($record_object, $failed);
				
				// wyświetla komunikat:
				$site_message = array(
					'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
				);
			}
		}
		else // nie uzupełniono wszystkich pól
		{
			// wyświetla pusty formularz:
			$site_content = $view_object->ShowForm($record_object, $failed);
			
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
	$_SESSION['form_fields']['autor'] = NULL;
	$_SESSION['form_fields']['email'] = NULL;
	$_SESSION['form_fields']['message'] = NULL;
	
	// wyświetla pusty formularz:
	$site_content = $view_object->ShowForm($record_object, $failed);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
