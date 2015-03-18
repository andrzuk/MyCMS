<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Roles_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$user_id = isset($_SESSION['form_fields']['user_id']) ? $_SESSION['form_fields']['user_id'] : 0;

		if (is_array($row))
		{
			$user_id = $row['user_id'];
		}

		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja roli';
		}
		else
		{
			$form_title = 'Nowa rola';
		}
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '600px';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		if (is_array($row))
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=edit&id=' . $user_id;
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

		// użytkownik:
		
		$main_options = Array();
		
		foreach ($import as $i => $j)
		{
			if ($i == 'users')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $u_id = $v;
						if ($k == 'user_login') $u_login = $v;						
						if ($k == 'imie') $u_imie = $v;						
						if ($k == 'nazwisko') $u_nazwisko = $v;						
						if ($k == 'status') $u_status = $v;						
					}
					if (is_array($row)) // edit
					{
						if ($u_id == $user_id)
						{
							$main_option = Array('value' => $u_id, 'caption' => $u_imie .' '. $u_nazwisko .' ('. $u_login .') - '. $u_status, 'selected' => 'selected');
							$main_options[] = $main_option;
						}
					}
					else // new
					{
						$main_option = Array('value' => $u_id, 'caption' => $u_imie .' '. $u_nazwisko .' ('. $u_login .') - '. $u_status);
						$main_options[] = $main_option;
					}
				}
			}
		}
		$form_data = Array(
						Array('type' => 'select', 'id' => 'user_id', 'name' => 'user_id', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Użytkownik', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// funkcje i dostęp:

		$form_data = Array(
			Array('type' => 'label', 'id' => '', 'name' => '', 'value' => NULL, 'style' => '')
			);
		$form_input = Array('caption' => 'Funkcje', 'data' => $form_data);
		$form_inputs[] = $form_input;

		foreach ($import as $i => $j)
		{
			if ($i == 'functions')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $id = $v;
						if ($k == 'function') $f_nazwa = $v;						
						if ($k == 'meaning') $f_znaczenie = $v;						
						if ($k == 'user_id') $u_id = $v;
						if ($k == 'function_id') $f_id = $v;
						if ($k == 'access') $f_access = $v;
					}
					
					$checked = $f_access ? 'checked' : NULL;
					
					$form_data = Array(
									Array('type' => 'checkbox', 'id' => 'function_'.$f_id, 'name' => 'function_'.$f_id, 'caption' => $f_nazwa . ' (' . $f_znaczenie . ')', $checked => $checked, 'onclick' => '')
									);
					$form_input = Array('caption_data' => $form_data, 'empty' => '');
					$form_inputs[] = $form_input;
				}
			}
		}
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'role_id', 'name' => 'role_id', 'value' => NULL)
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
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
			array('width' => '20%', 'align' => 'left', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
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
}

?>
