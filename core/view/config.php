<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Config_View
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
		$key_name = isset($_SESSION['form_fields']['key_name']) ? $_SESSION['form_fields']['key_name'] : NULL;
		$key_value = isset($_SESSION['form_fields']['key_value']) ? $_SESSION['form_fields']['key_value'] : NULL;
		$field_type = isset($_SESSION['form_fields']['field_type']) ? $_SESSION['form_fields']['field_type'] : NULL;
		$active = 1; 
		$meaning = isset($_SESSION['form_fields']['meaning']) ? $_SESSION['form_fields']['meaning'] : NULL;
		$modified = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$key_name = $row['key_name'];
			$key_value = $row['key_value'];
			$field_type = $row['field_type'];
			$active = $row['active']; 
			$meaning = $row['meaning'];
			$modified = $row['modified'];
		}
				
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja konfiguracji';
		}
		else
		{
			$form_title = 'Nowa konfiguracja';
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
						Array('type' => 'text', 'id' => 'key_name', 'name' => 'key_name', 'caption' => '', 'value' => $key_name, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Nazwa klucza', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'meaning', 'name' => 'meaning', 'caption' => '', 'value' => $meaning, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Znaczenie', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$sel = array('1' => '', '2' => '', '3' => '');
		$sel[$field_type] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => '1', 'caption' => 'string (pole tekstowe - krótkie)', $sel['1'] => $sel['1']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'area (obszar opisowy - długi)', $sel['2'] => $sel['2']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'option (wartość true - false)', $sel['3'] => $sel['3']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'field_type', 'name' => 'field_type', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Typ wartości', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		if ($field_type == 1) // pole tekstowe
		{
			$form_data = Array(
							Array('type' => 'text', 'id' => 'key_value', 'name' => 'key_value', 'caption' => '', 'value' => $key_value, 'style' => 'width: 96%;')
							);
		}
		else if ($field_type == 2) // obszar
		{
			$form_data = Array(
							Array('type' => 'textarea', 'id' => 'key_value', 'name' => 'key_value', 'value' => $key_value, 'style' => 'height: 90px; width: 96%;')
							);
		}
		else if ($field_type == 3) // opcja
		{
			$option = Array('', '');
			if ($key_value == 'true') $option[0] = 'checked';
			else $option[1] = 'checked';

			$form_data = Array(
							Array('type' => 'radio', 'id' => 'key_value_1', 'name' => 'key_value', 'value' => 'true', 'caption' => 'true (włączone)', $option[0] => $option[0], 'onclick' => ''),
							Array('type' => 'radio', 'id' => 'key_value_2', 'name' => 'key_value', 'value' => 'false', 'caption' => 'false (wyłączone)', $option[1] => $option[1], 'onclick' => '')
							);
		}
		else // domyślnie
		{
			$form_data = Array(
							Array('type' => 'text', 'id' => 'key_value', 'name' => 'key_value', 'caption' => '', 'value' => $key_value, 'style' => 'width: 96%;')
							);
		}
		$form_input = Array('caption' => 'Wartość (ustawienie)', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$actv = Array('', '');
		if ($active) $actv[0] = 'selected';
		else $actv[1] = 'selected';

		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => 'tak', $actv[0] => $actv[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '0', 'caption' => 'nie', $actv[1] => $actv[1]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'active', 'name' => 'active', 'option' => $main_options, 'style' => 'width: 100px;', 'description' => '(czy jest uwzględniane w serwisie)')
						);
		$form_input = Array('caption' => 'Aktywne', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		if (is_array($row))
		{
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $modified, 'style' => '')
							);
			$form_input = Array('caption' => 'Data modyfikacji', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'config_id', 'name' => 'config_id', 'value' => $id)
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
		
		$list_title = strtoupper(MODULE_NAME) . ' - Wszystkie';

		$list_image = 'img/32x32/options.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '23%', 'align' => 'left', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
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