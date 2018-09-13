<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Messages_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$id = 0;
		$client_name = isset($_SESSION['form_fields']['client_name']) ? $_SESSION['form_fields']['client_name'] : NULL;
		$client_email = isset($_SESSION['form_fields']['client_email']) ? $_SESSION['form_fields']['client_email'] : NULL;
		$message_content = isset($_SESSION['form_fields']['message_content']) ? $_SESSION['form_fields']['message_content'] : NULL;
		$requested = NULL;
		$send_date = NULL;
		$close_date = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$client_name = $row['client_name'];
			$client_email = $row['client_email'];
			$message_content = $row['message_content'];
			$requested = $row['requested']; 
			$send_date = $row['send_date'];
			$close_date = $row['close_date'];
		}
				
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Edycja wiadomości';
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '100%';
		$form_widths = Array('15%', '85%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		if (is_array($row))
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=edit&id=' . $id;
		}		
		else
		{
			$form_action = 'index.php?route=' . MODULE_NAME;
		}
		
		$main_form->set_action($form_action);
		
		// required:
		
		$main_form->set_required($required);
		
		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		if (is_array($row))
		{
			$form_data = Array(
				Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $id, 'style' => '')
				);
			$form_input = Array('caption' => 'Id', 'data' => $form_data);
			$form_inputs[] = $form_input;

		}

		$form_data = Array(
						Array('type' => 'text', 'id' => 'client_name', 'name' => 'client_name', 'caption' => '', 'value' => $client_name, 'style' => 'width: 97%;')
						);
		$form_input = Array('caption' => 'Imię (nick)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'client_email', 'name' => 'client_email', 'caption' => '', 'value' => $client_email, 'style' => 'width: 97%;')
						);
		$form_input = Array('caption' => 'Adres e-mail', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'message_content', 'name' => 'message_content', 'value' => $message_content, 'style' => 'height: 200px; width: 97%;')
						);
		$form_input = Array('caption' => 'Treść wiadomości (pytanie, opinia, komentarz, uwagi itp.)', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		if (is_array($row))
		{
			$stat = Array('', '');
			if ($requested) $stat[0] = 'selected';
			else $stat[1] = 'selected';

			$main_options = Array();
			
			$main_option = Array('value' => '1', 'caption' => 'Nadesłana', $stat[0] => $stat[0]);
			$main_options[] = $main_option;
			$main_option = Array('value' => '0', 'caption' => 'Zatwierdzona', $stat[1] => $stat[1]);
			$main_options[] = $main_option;
			$form_data = Array(
							Array('type' => 'select', 'id' => 'requested', 'name' => 'requested', 'option' => $main_options, 'style' => 'width: 50%;', 'description' => '(stan wiadomości)')
							);
			$form_input = Array('caption' => 'Status', 'data' => $form_data);
			$form_inputs[] = $form_input;

			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $send_date, 'style' => '')
							);
			$form_input = Array('caption' => 'Data zgłoszenia', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		
		$main_form->set_inputs($form_inputs);
		
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
		
		$mode = isset($_SESSION['mode']) ? $_SESSION['mode'] : NULL;
		
		switch ($mode)
		{
			case 1:
				$list_title = strtoupper(MODULE_NAME) . ' - Nadesłane';
				break;
			case 2:
				$list_title = strtoupper(MODULE_NAME) . ' - Zatwierdzone';
				break;
			default:
				$list_title = strtoupper(MODULE_NAME) . ' - Wszystkie';
				break;
		}
		$list_image = 'img/32x32/mail.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '40%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
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
		$view_image = 'img/32x32/list.png';
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
}

?>