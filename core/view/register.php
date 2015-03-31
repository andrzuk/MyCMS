<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Register_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $failed)
	{
		$login = isset($_SESSION['form_fields']['login']) ? $_SESSION['form_fields']['login'] : NULL;
		$imie = isset($_SESSION['form_fields']['imie']) ? $_SESSION['form_fields']['imie'] : NULL;
		$nazwisko = isset($_SESSION['form_fields']['nazwisko']) ? $_SESSION['form_fields']['nazwisko'] : NULL;
		$email = isset($_SESSION['form_fields']['email']) ? $_SESSION['form_fields']['email'] : NULL;
		$password = isset($_SESSION['form_fields']['password']) ? $_SESSION['form_fields']['password'] : NULL;
		$pesel = isset($_SESSION['form_fields']['pesel']) ? $_SESSION['form_fields']['pesel'] : NULL;

		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Rejestracja';
		$form_image = 'img/32x32/list_checked.png';
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
						Array('type' => 'text', 'id' => 'imie', 'name' => 'imie', 'caption' => '', 'value' => $imie, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Imię', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'nazwisko', 'name' => 'nazwisko', 'caption' => '', 'value' => $nazwisko, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Nazwisko', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email', 'name' => 'email', 'caption' => '', 'value' => $email, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'E-mail', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'pesel', 'name' => 'pesel', 'caption' => '', 'value' => $pesel, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Pesel', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'login', 'name' => 'login', 'caption' => '', 'value' => $login, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Login', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'password', 'id' => 'password', 'name' => 'password', 'caption' => '', 'value' => $password, 'style' => 'width: 96%;'),
						);
		$form_input = Array('caption' => 'Hasło', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$main_form->set_inputs($form_inputs);
		
		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'register_button', 'name' => 'register_button', 'value' => 'Zapisz', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// links:
		
		$form_links = Array(
						Array('address' => 'index.php?route=login', 'caption' => 'Mam już konto w serwisie')
						);
		$main_form->set_links($form_links);		

		// render:
		
		$site_content = $main_form->build_form();

		// Form Generator.
		
		return $site_content;
	}
	
	/*
	 * Panel
	 */

	public function ShowDetails($row, $columns)
	{
		$site_content = NULL;
		
		$view_title = 'Szczegóły użytkownika';
		$view_image = 'img/32x32/contact.png';
		$view_width = '600px';

		$site_content .= '<table class="Table" width="'.$view_width.'" cellpadding="2" cellspacing="1" align="center">';

		$site_content .= '<tr>';
		$site_content .= '<th class="FormTitleBar" colspan="2">';
		$site_content .= '<span class="FormIcon">';
		$site_content .= '<img src="'.$view_image.'" alt="'.$view_title.'" />';
		$site_content .= '</span>';
		$site_content .= '<span class="FormTitle">';
		$site_content .= $view_title;
		$site_content .= '</span>';
		$site_content .= '</th>';
		$site_content .= '</tr>';

		foreach ($columns as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'db_name') $db_name = $value;
				if ($key == 'column_name') $column_name = $value;
				if ($key == 'color') $color = $value;
			}

			$site_content .= '<tr>';
			$site_content .= '<td class="DataCell">';
			$site_content .= $column_name . ':';
			$site_content .= '</td>';
			$site_content .= '<td class="DetailsCell" style="color: ' . $color . ';">';
			$site_content .= '<b>' . $row[$db_name] . '</b>';
			$site_content .= '</td>';
			$site_content .= '</tr>';
		}
		
		$site_content .= '</table>';
		
		return $site_content;
	}
}

?>