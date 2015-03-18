<?php

class Init_View
{
	public function __construct()
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $failed)
	{
		$short_title = isset($_SESSION['form_fields']['short_title']) ? $_SESSION['form_fields']['short_title'] : NULL;
		$main_title = isset($_SESSION['form_fields']['main_title']) ? $_SESSION['form_fields']['main_title'] : NULL;
		$main_description = isset($_SESSION['form_fields']['main_description']) ? $_SESSION['form_fields']['main_description'] : NULL;
		$main_keywords = isset($_SESSION['form_fields']['main_keywords']) ? $_SESSION['form_fields']['main_keywords'] : NULL;
		$base_domain = isset($_SESSION['form_fields']['base_domain']) ? $_SESSION['form_fields']['base_domain'] : NULL;
		$email_sender_address = isset($_SESSION['form_fields']['email_sender_address']) ? $_SESSION['form_fields']['email_sender_address'] : NULL;
		$email_admin_address = isset($_SESSION['form_fields']['email_admin_address']) ? $_SESSION['form_fields']['email_admin_address'] : NULL;
		$email_report_address = isset($_SESSION['form_fields']['email_report_address']) ? $_SESSION['form_fields']['email_report_address'] : NULL;
		$admin_name = isset($_SESSION['form_fields']['admin_name']) ? $_SESSION['form_fields']['admin_name'] : NULL;
		$admin_login = isset($_SESSION['form_fields']['admin_login']) ? $_SESSION['form_fields']['admin_login'] : NULL;
		$admin_password = isset($_SESSION['form_fields']['admin_password']) ? $_SESSION['form_fields']['admin_password'] : NULL;

		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once '../lib/gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Ustawienia';
		$form_image = '../img/32x32/list_checked.png';
		$form_width = '75%';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php';
		
		$main_form->set_action($form_action);
		
		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'short_title', 'name' => 'short_title', 'caption' => '', 'value' => $short_title, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Krótka nazwa serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'main_title', 'name' => 'main_title', 'caption' => '', 'value' => $main_title, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Tytuł serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'main_description', 'name' => 'main_description', 'value' => $main_description, 'style' => 'height: 50px; width: 96%;')
						);
		$form_input = Array('caption' => 'Opis serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'main_keywords', 'name' => 'main_keywords', 'value' => $main_keywords, 'style' => 'height: 50px; width: 96%;')
						);
		$form_input = Array('caption' => 'Keywords serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'base_domain', 'name' => 'base_domain', 'caption' => '', 'value' => $base_domain, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Domena (adres) serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email_sender_address', 'name' => 'email_sender_address', 'caption' => '', 'value' => $email_sender_address, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Konto e-mail nadawcze', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email_admin_address', 'name' => 'email_admin_address', 'caption' => '', 'value' => $email_admin_address, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Konto e-mail administratora', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email_report_address', 'name' => 'email_report_address', 'caption' => '', 'value' => $email_report_address, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Konto e-mail odbioru raportów', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'admin_name', 'name' => 'admin_name', 'caption' => '', 'value' => $admin_name, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Imię i nazwisko administratora', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'admin_login', 'name' => 'admin_login', 'caption' => '', 'value' => $admin_login, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Login administratora', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'password', 'id' => 'admin_password', 'name' => 'admin_password', 'caption' => '', 'value' => $admin_password, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Hasło administratora', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$main_form->set_inputs($form_inputs);
		
		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Zapisz', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// links:
		
		$form_links = Array(
						Array('address' => '../index.php', 'caption' => 'Strona główna serwisu')
						);
		$main_form->set_links($form_links);		

		// render:
		
		$site_content = $main_form->build_form();

		// Form Generator.
		
		return $site_content;
	}
	
	/*
	 * Pomoc
	 */
	 
	public function ShowIntro($content)
	{
		$site_content = NULL;
		
		$site_content .= '<p class="Intro">';
		$site_content .= $content;
		$site_content .= '</p>';
		
		return $site_content;
	}
}

?>