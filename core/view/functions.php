<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Functions_View
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
		$function = isset($_SESSION['form_fields']['function']) ? $_SESSION['form_fields']['function'] : NULL;
		$meaning = isset($_SESSION['form_fields']['meaning']) ? $_SESSION['form_fields']['meaning'] : NULL;
		$module = isset($_SESSION['form_fields']['module']) ? $_SESSION['form_fields']['module'] : NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$function = $row['function'];
			$meaning = $row['meaning'];
			$module = $row['module'];
		}
				
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja funkcji';
		}
		else
		{
			$form_title = 'Nowa funkcja';
		}
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '600px';
		$form_widths = Array('20%', '80%');
		
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
						Array('type' => 'text', 'id' => 'function', 'name' => 'function', 'caption' => '', 'value' => $function, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Funkcja serwisu', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$form_data = Array(
						Array('type' => 'text', 'id' => 'meaning', 'name' => 'meaning', 'caption' => '', 'value' => $meaning, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Znaczenie funkcji', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'module', 'name' => 'module', 'caption' => '', 'value' => $module, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Moduł', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'function_id', 'name' => 'function_id', 'value' => $id)
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
		$list_image = 'img/32x32/node_tree.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '28%', 'align' => 'left', 'visible' => '1'),
			array('width' => '35%', 'align' => 'left', 'visible' => '1'),
			array('width' => '22%', 'align' => 'left', 'visible' => '1'),
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
