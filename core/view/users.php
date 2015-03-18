<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Users_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$actv = array('', '');

		$id = 0;
		$status = 3;
		$user_login = isset($_SESSION['form_fields']['user_login']) ? $_SESSION['form_fields']['user_login'] : NULL;
		$imie = isset($_SESSION['form_fields']['imie']) ? $_SESSION['form_fields']['imie'] : NULL;
		$nazwisko = isset($_SESSION['form_fields']['nazwisko']) ? $_SESSION['form_fields']['nazwisko'] : NULL;
		$email = isset($_SESSION['form_fields']['email']) ? $_SESSION['form_fields']['email'] : NULL;
		$ulica = isset($_SESSION['form_fields']['ulica']) ? $_SESSION['form_fields']['ulica'] : NULL;
		$kod = isset($_SESSION['form_fields']['kod']) ? $_SESSION['form_fields']['kod'] : NULL;
		$miasto = isset($_SESSION['form_fields']['miasto']) ? $_SESSION['form_fields']['miasto'] : NULL;
		$pesel = isset($_SESSION['form_fields']['pesel']) ? $_SESSION['form_fields']['pesel'] : NULL;
		$telefon = isset($_SESSION['form_fields']['telefon']) ? $_SESSION['form_fields']['telefon'] : NULL;
		$active = 1;
		$data_modyfikacji = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$status = $row['status'];
			$user_login = $row['user_login'];
			$imie = $row['imie'];
			$nazwisko = $row['nazwisko'];
			$email = $row['email'];
			$ulica = $row['ulica'];
			$kod = $row['kod'];
			$miasto = $row['miasto'];
			$pesel = $row['pesel'];
			$telefon = $row['telefon'];
			$active = $row['active']; 			
			$data_modyfikacji = $row['data_modyfikacji'];
		}

		switch ($row['status'])
		{
			case 'Administratorzy':
				$status = 1;
				break;
			case 'Operatorzy':
				$status = 2;
				break;
			case 'Użytkownicy':
				$status = 3;
				break;
		}
		
		switch ($row['active'])
		{
			case 'Tak':
				$active = 1;
				break;
			case 'Nie':
				$active = 0;
				break;
		}
		
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja konta';
		}
		else
		{
			$form_title = 'Nowe konto';
		}
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '600px';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		if (is_array($row))
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=edit&id=' . $id;
		}		
		else
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=add';
		}
		
		$main_form->set_action($form_action);
		
		// import:
		
		foreach ($import as $i => $j)
		{
			if ($i == 'user_status')
			{
				$user_status = $j;
			}
		}		
			
		// required:
		
		$main_form->set_required($required);
		
		// failed:
		
		$main_form->set_failed($failed);
		
		// inputs:
		
		if (is_array($row)) // edycja
		{
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => '<b>'. $user_login .'</b>', 'style' => '')
							);
			$form_input = Array('caption' => 'Login', 'data' => $form_data);
			$form_inputs[] = $form_input;

			$form_data = Array(
							Array('type' => 'hidden', 'id' => 'user_login', 'name' => 'user_login', 'value' => $user_login)
							);
			$form_hiddens[] = $form_data;
		}
		else // nowe
		{
			$form_data = Array(
							Array('type' => 'text', 'id' => 'user_login', 'name' => 'user_login', 'caption' => '', 'value' => $user_login, 'style' => 'width: 96%;')
							);
			$form_input = Array('caption' => 'Login', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
				
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
						Array('type' => 'text', 'id' => 'ulica', 'name' => 'ulica', 'caption' => '', 'value' => $ulica, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Ulica, nr domu', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'kod', 'name' => 'kod', 'caption' => '', 'value' => $kod, 'style' => 'width: 17%;'),
						Array('type' => 'text', 'id' => 'miasto', 'name' => 'miasto', 'caption' => '&nbsp;', 'value' => $miasto, 'style' => 'width: 76%;')
						);
		$form_input = Array('caption' => 'Kod pocztowy, miasto', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'pesel', 'name' => 'pesel', 'caption' => '', 'value' => $pesel, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'PESEL', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'telefon', 'name' => 'telefon', 'caption' => '', 'value' => $telefon, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Telefon', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'email', 'name' => 'email', 'caption' => '', 'value' => $email, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'E-mail', 'data' => $form_data);
		$form_inputs[] = $form_input;

		if ($user_status == 1) // admin
		{
			$sel = array('', '', '');
			$sel[$status - 1] = 'selected';

			$main_options = Array(
				Array('value' => '1', 'caption' => 'admin (poziom uprawnień: 1)', $sel[0] => $sel[0]),
				Array('value' => '2', 'caption' => 'operator (poziom uprawnień: 2)', $sel[1] => $sel[1]),
				Array('value' => '3', 'caption' => 'user (poziom uprawnień: 3)', $sel[2] => $sel[2]),
			);
			
			$form_data = Array(
							Array('type' => 'select', 'id' => 'status', 'name' => 'status', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
							);
			$form_input = Array('caption' => 'Profil konta', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		else // nie admin - nie ma możliwości zmiany statusu
		{
			$form_data = Array(
							Array('type' => 'hidden', 'id' => 'status', 'name' => 'status', 'value' => $status)
							);
			$form_hiddens[] = $form_data;
		}

		$actv = array('', '');
		if ($active == 1) $actv[0] = 'selected';
		else $actv[1] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => '1', 'caption' => 'tak', $actv[0] => $actv[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '0', 'caption' => 'nie', $actv[1] => $actv[1]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'active', 'name' => 'active', 'option' => $main_options, 'description' => '', 'style' => 'width: 100px;')
						);
		$form_input = Array('caption' => 'Aktywne', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		if (is_array($row)) // edycja
		{
			$form_data = Array(
							Array('type' => 'password', 'id' => 'user_password', 'name' => 'user_password', 'caption' => '(nie wpisywać, jeśli bez zmian)', 'value' => '', 'style' => 'width: 96%;'),
							);
		}
		else // nowe
		{
			$form_data = Array(
							Array('type' => 'password', 'id' => 'user_password', 'name' => 'user_password', 'caption' => '', 'value' => '', 'style' => 'width: 96%;'),
							);
		}
		$form_input = Array('caption' => 'Hasło', 'data' => $form_data);
		$form_inputs[] = $form_input;

		if (is_array($row)) // edycja
		{
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $data_modyfikacji, 'style' => '')
							);
			$form_input = Array('caption' => 'Data modyfikacji', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'user_status', 'name' => 'user_status', 'value' => $status)
						);
		$form_hiddens[] = $form_data;
			
		$main_form->set_hiddens($form_hiddens);

		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Zapisz', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;		
		$form_data = Array('type' => 'submit', 'id' => 'update_button', 'name' => 'update_button', 'value' => 'Zamknij', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		$form_data = Array('type' => 'submit', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content = $main_form->build_form();

		// Form Generator.
		
		return $site_content;
	}
	
	/*
	 * Lista
	 */
	 
	public function ShowList($list, $columns, $params)
	{
		// List Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'list.php';
		
		$main_list = new ListBuilder();
		
		$list_title = 'Lista - wszystkie pozycje';
		$list_image = 'img/32x32/application_side_list.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '4%', 'align' => 'center', 'visible' => '1'),
			array('width' => '7%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'left', 'visible' => '0'),
			array('width' => '9%', 'align' => 'left', 'visible' => '1'),
			array('width' => '9%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '2%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '0'),
			array('width' => '10%', 'align' => 'left', 'visible' => '0'),
			array('width' => '10%', 'align' => 'left', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '8%', 'align' => 'center', 'visible' => '1'),
			array('width' => '8%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '7%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
			array('action' => 'edit', 'icon' => 'edit.png', 'title' => 'Edytuj'),
			array('action' => 'delete', 'icon' => 'trash.png', 'title' => 'Usuń'),
		);
		
		$main_list->set_actions($col_actions);

		// render:
		
		$site_content = $main_list->build_list();
		
		// List Generator.
		
		return $site_content;
	}
	
	/*
	 * Szczegóły
	 */
	
	public function ShowRecord($row, $columns)
	{
		// View Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'view.php';
		
		$main_view = new ViewBuilder();
		
		$view_title = 'Podgląd';
		$view_image = 'img/32x32/list_edit.png';
		$view_width = '600px';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$main_view->set_row($row);
		
		$main_view->set_columns($columns);
		
		$main_view->set_buttons(array('edit', 'cancel',));

		// render:
		
		$site_content = $main_view->build_view();
		
		// View Generator.
		
		return $site_content;
	}
	
	/*
	 * Panel
	 */

	public function ShowDetails($row, $columns)
	{
		$site_content = NULL;
		
		$view_title = 'Szczegóły';
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
			$site_content .= '<td class="FormCell">';
			$site_content .= $column_name . ':';
			$site_content .= '</td>';
			$site_content .= '<td class="DetailsCell" style="color: ' . $color . ';">';
			$site_content .= '<b>' . $row[$db_name] . '</b>';
			$site_content .= '</td>';
			$site_content .= '</tr>';
		}

		$site_content .= '<tr>';
		$site_content .= '<td colspan="2" class="ButtonBar">';
		$site_content .= '<table cellpadding="1" cellspacing="1" align="right">';
		$site_content .= '<tr>';
		$site_content .= '<td>';
		$site_content .= '<form action="index.php?route=users&action=edit&id=' . $row['id'] . '" method="post">';
		$site_content .= '<input type="submit" name="edit" value="Edytuj" class="Button" style="width: 80px;" />';
		$site_content .= '</form>';
		$site_content .= '</td>';
		$site_content .= '<td>';
		$site_content .= '<form action="index.php?route=users" method="post">';
		$site_content .= '<input type="submit" name="cancel" value="Anuluj" class="Button" style="width: 80px;" />';
		$site_content .= '</form>';
		$site_content .= '</td>';
		$site_content .= '</tr>';
		$site_content .= '</table>';
		$site_content .= '</td>';
		$site_content .= '</tr>';

		$site_content .= '</table>';
		
		return $site_content;
	}
}

?>