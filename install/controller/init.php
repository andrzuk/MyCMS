<?php

define ('MODULE_NAME', 'init');

$content_title = 'Instalacja serwisu';

include 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Init_Model($db);

include 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Init_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

$intro = $model_object->GetIntro();

// brakujące pola:
$failed = array();

if (isset($_POST['save_button'])) // obsługa formularza
{
	$main_title = isset($_POST['main_title']) ? htmlspecialchars(trim($_POST['main_title'])) : NULL;
	$main_description = isset($_POST['main_description']) ? htmlspecialchars(trim($_POST['main_description'])) : NULL;
	$main_keywords = isset($_POST['main_keywords']) ? htmlspecialchars(trim($_POST['main_keywords'])) : NULL;
	$base_domain = isset($_POST['base_domain']) ? htmlspecialchars(trim($_POST['base_domain'])) : NULL;
	$short_title = isset($_POST['short_title']) ? htmlspecialchars(trim($_POST['short_title'])) : NULL;
	$email_sender_address = isset($_POST['email_sender_address']) ? htmlspecialchars(trim($_POST['email_sender_address'])) : NULL;
	$email_admin_address = isset($_POST['email_admin_address']) ? htmlspecialchars(trim($_POST['email_admin_address'])) : NULL;
	$email_report_address = isset($_POST['email_report_address']) ? htmlspecialchars(trim($_POST['email_report_address'])) : NULL;
	$admin_name = isset($_POST['admin_name']) ? htmlspecialchars(trim($_POST['admin_name'])) : NULL;
	$admin_login = isset($_POST['admin_login']) ? htmlspecialchars(trim($_POST['admin_login'])) : NULL;
	$admin_password = isset($_POST['admin_password']) ? htmlspecialchars(trim($_POST['admin_password'])) : NULL;
	
	// zapamiętuje dane w formularzu:
	$_SESSION['form_fields']['main_title'] = $main_title;
	$_SESSION['form_fields']['main_description'] = $main_description;
	$_SESSION['form_fields']['main_keywords'] = $main_keywords;
	$_SESSION['form_fields']['base_domain'] = $base_domain;
	$_SESSION['form_fields']['short_title'] = $short_title;
	$_SESSION['form_fields']['email_sender_address'] = $email_sender_address;
	$_SESSION['form_fields']['email_admin_address'] = $email_admin_address;
	$_SESSION['form_fields']['email_report_address'] = $email_report_address;
	$_SESSION['form_fields']['admin_name'] = $admin_name;
	$_SESSION['form_fields']['admin_login'] = $admin_login;
	$_SESSION['form_fields']['admin_password'] = $admin_password;

	// wymagane pola:
	$required = array(
		'main_title' => $main_title, 
		'main_description' => $main_description, 
		'main_keywords' => $main_keywords, 
		'base_domain' => $base_domain, 
		'short_title' => $short_title,
		'email_sender_address' => $email_sender_address,
		'email_admin_address' => $email_admin_address,
		'email_report_address' => $email_report_address,
		'admin_name' => $admin_name, 
		'admin_login' => $admin_login, 
		'admin_password' => $admin_password, 
	);

	foreach ($required as $k => $v)
		if (empty($v)) $failed[] = $k;
	
	// sprawdzanie czy uzupełniono wszystkie dane:
	if (empty($failed))
	{
		$record_object = array(
			'main_title' => $main_title, 
			'main_description' => $main_description, 
			'main_keywords' => $main_keywords, 
			'base_domain' => $base_domain, 
			'short_title' => $short_title,
			'email_sender_address' => $email_sender_address,
			'email_admin_address' => $email_admin_address,
			'email_report_address' => $email_report_address,
			'admin_name' => $admin_name, 
			'admin_login' => $admin_login, 
			'admin_password' => $admin_password, 
		);

		$input_check = NULL;
		foreach ($record_object as $k => $v) 
			$input_check .= $v .' ';

		include '../' . LIB_DIR . 'validator.php';
		
		$validator_object = new Validator();
		
		$check_email_1 = $validator_object->check_email($email_sender_address);
		$check_email_2 = $validator_object->check_email($email_admin_address);
		$check_email_3 = $validator_object->check_email($email_report_address);
		
		if ($check_email_1 && $check_email_2 && $check_email_3) // email poprawny
		{
			// zapisuje ustawienia do bazy:
			
			$result = $model_object->SaveSettings($record_object);
			
			if ($result) // zapis się powiódł
			{
				$site_dialog = array(
					'INFORMATION',
					'Ustawienia',
					'Ustawienia serwisu zostały poprawnie zapisane. Usuń z serwisu katalog "<b>install</b>".',
					array(
						array(
							'../index.php?route=login', 'Zaloguj'
						),
						array(
							'../index.php', 'Zamknij'
						),
					)
				);
			}
			else // inicjalizacja się nie powiodła
			{
				// wyświetla pusty formularz:
				$site_content = $view_object->ShowIntro($intro) . $view_object->ShowForm(NULL, $failed);
				
				// wyświetla komunikat:
				$site_message = array(
					'ERROR', 'Inicjalizacja serwisu nie powiodła się. Sprawdź poprawność połączenia z bazą danych.'
				);
			}
		}
		else // email niepoprawny
		{
			// oznacza niepoprawne pole:
			if (!$check_email_1) $failed[] = 'email_sender_address';
			if (!$check_email_2) $failed[] = 'email_admin_address';
			if (!$check_email_3) $failed[] = 'email_report_address';
			
			// wyświetla pusty formularz:
			$site_content = $view_object->ShowIntro($intro) . $view_object->ShowForm(NULL, $failed);
			
			// wyświetla komunikat:
			$site_message = array(
				'ERROR', 'Nieprawidłowy adres e-mail. Proszę poprawić.'
			);
		}
	}
	else // nie uzupełniono wszystkich pól
	{
		// wyświetla pusty formularz:
		$site_content = $view_object->ShowIntro($intro) . $view_object->ShowForm(NULL, $failed);
		
		// wyświetla komunikat:
		$site_message = array(
			'WARNING', 'Nie wypełniono wszystkich wymaganych pól. Proszę uzupełnić.'
		);
	}
}
else // pusty formularz
{
	// czyści dane w formularzu:
	$_SESSION['form_fields']['main_title'] = NULL;
	$_SESSION['form_fields']['main_description'] = NULL;
	$_SESSION['form_fields']['main_keywords'] = NULL;
	$_SESSION['form_fields']['base_domain'] = NULL;
	$_SESSION['form_fields']['short_title'] = NULL;
	$_SESSION['form_fields']['email_sender_address'] = NULL;
	$_SESSION['form_fields']['email_admin_address'] = NULL;
	$_SESSION['form_fields']['email_report_address'] = NULL;
	$_SESSION['form_fields']['admin_name'] = NULL;
	$_SESSION['form_fields']['admin_login'] = NULL;
	$_SESSION['form_fields']['admin_password'] = NULL;
	
	// wyświetla pusty formularz:
	$site_content = $view_object->ShowIntro($intro) . $view_object->ShowForm(NULL, $failed);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'controller/route.php';

?>
