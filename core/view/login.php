<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Login_View
{
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $failed)
	{
		$main_login = isset($_SESSION['form_fields']['login']) ? $_SESSION['form_fields']['login'] : NULL;
		$main_password = isset($_SESSION['form_fields']['password']) ? $_SESSION['form_fields']['password'] : NULL;

		// Form Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Logowanie';
		$form_image = 'img/32x32/report_key.png';
		$form_width = '400px';
		$form_widths = Array('40%', '60%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php?route=' . MODULE_NAME;
		
		$main_form->set_action($form_action);
		
		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'login', 'name' => 'login', 'caption' => '', 'value' => $main_login, 'style' => 'width: 95%;')
						);
		$form_input = Array('caption' => 'Login lub e-mail', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'password', 'id' => 'password', 'name' => 'password', 'caption' => '', 'value' => $main_password, 'style' => 'width: 95%;')
						);
		$form_input = Array('caption' => 'Hasło', 'data' => $form_data);
		$form_inputs[] = $form_input;
				
		$main_form->set_inputs($form_inputs);
		
		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'login_button', 'name' => 'login_button', 'value' => 'Zaloguj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// links:
		
		$form_links = Array(
						Array('address' => 'index.php?route=password', 'caption' => 'Nie pamiętam hasła logowania')
						);
		$main_form->set_links($form_links);		

		// render:
		
		$site_content = $main_form->build_form();
		
		// Form Generator.
		
		return $site_content;
	}
}

?>
