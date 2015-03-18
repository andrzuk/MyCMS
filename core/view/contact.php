<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Contact_View
{
	private $setting;
	
	public function __construct($db)
	{
		$this->setting = new Settings($db);
	}
	
	public function ShowForm($row, $failed)
	{
		$site_content = NULL;
		$site_modified = NULL;
		
		// tekst wprowadzający:
		
		$site_content .= '<div class="PageMainContent">';
		
		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'contents') $site_content .= $value;
			}
		}
		
		$site_content .= '</div>';
		
		// formularz wysyłania wiadomości:
		
		$autor = isset($_SESSION['form_fields']['autor']) ? $_SESSION['form_fields']['autor'] : NULL;
		$email = isset($_SESSION['form_fields']['email']) ? $_SESSION['form_fields']['email'] : NULL;
		$message = isset($_SESSION['form_fields']['message']) ? $_SESSION['form_fields']['message'] : NULL;

		// Form Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Napisz do nas';
		$form_image = 'img/32x32/mail.png';
		$form_width = '600px';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php?route=' . MODULE_NAME;
		
		$main_form->set_action($form_action);
		
		// failed:
		
		$main_form->set_failed($failed);

		// odczytuje z konfiguracji aktywność edytora CkEdit:
		$using_office_editor = $this->setting->get_config_key('using_office_editor');
		
		// CkEditor:
		
		$main_form->set_editor($using_office_editor);
		
		// inputs:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'autor', 'name' => 'autor', 'caption' => '', 'value' => $autor, 'style' => 'width: 97%;')
						);
		$form_input = Array('caption' => 'Imię (nick)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email', 'name' => 'email', 'caption' => '', 'value' => $email, 'style' => 'width: 97%;')
						);
		$form_input = Array('caption' => 'Adres e-mail', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'message', 'name' => 'message', 'value' => $message, 'style' => 'height: 200px; width: 97%;')
						);
		$form_input = Array('caption' => 'Treść wiadomości', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'checkbox', 'id' => 'send_copy', 'name' => 'send_copy', 'caption' => 'Przyślij kopię tej wiadomości na mój adres e-mail', 'checked' => 'checked', 'onclick' => '')
						);
		$form_input = Array('caption_data' => $form_data, 'empty' => '');
		$form_inputs[] = $form_input;
		
		$main_form->set_inputs($form_inputs);

		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'send_button', 'name' => 'send_button', 'value' => 'Wyślij', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content .= $main_form->build_form();
		
		// Form Generator.
		
		return $site_content;
	}
}

?>
