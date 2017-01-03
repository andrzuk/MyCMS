<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Categories_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$default_link = 'index.php?route=category&id=NULL';
		
		$id = 0;
		$type = 1;
		$level = 1;
		$parent_id = isset($_SESSION['form_fields']['parent_id']) ? $_SESSION['form_fields']['parent_id'] : 0;
		$permission = isset($_SESSION['form_fields']['permission']) ? $_SESSION['form_fields']['permission'] : 4;
		$item_order = isset($_SESSION['form_fields']['item_order']) ? $_SESSION['form_fields']['item_order'] : 1;
		$caption = isset($_SESSION['form_fields']['caption']) ? $_SESSION['form_fields']['caption'] : NULL;
		$link = isset($_SESSION['form_fields']['link']) ? $_SESSION['form_fields']['link'] : $default_link;
		$icon_id = 0;
		$page_id = 0;
		$visible = isset($_SESSION['form_fields']['visible']) ? $_SESSION['form_fields']['visible'] : 1;
		$target = 0;
		$modified = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$type = $row['type'];
			$level = $row['level'];
			$parent_id = $row['parent_id'];
			$permission = $row['permission'];
			$item_order = $row['item_order']; 
			$caption = $row['caption'];
			$link = $row['link'];
			$icon_id = $row['icon_id'];
			$page_id = $row['page_id'];
			$visible = $row['visible'];
			$target = $row['target'];
			$modified = $row['modified'];
		}

		$target_chkd = $target ? 'checked' : NULL;
				
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja kategorii';
		}
		else
		{
			$form_title = 'Nowa kategoria';
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
			// id:
			
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $id, 'style' => '')
							);
			$form_input = Array('caption' => 'Id', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}

		// rodzaj:
		
		$sel = Array('', '');
		if ($type == 1) $sel[0] = 'selected';
		else $sel[1] = 'selected';

		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => '1 (pasek nawigacji)', $sel[0] => $sel[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => '2 (menu boczne)', $sel[1] => $sel[1]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'type', 'name' => 'type', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Rodzaj (która sekcja)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// rodzic:
		
		$main_options = Array();
		
		if ($parent_id == 0)
			$main_option = Array('value' => '0', 'caption' => 'Brak (id=0)', 'selected' => 'selected');
		else
			$main_option = Array('value' => '0', 'caption' => 'Brak (id=0)');
		$main_options[] = $main_option;
					
		foreach ($import as $i => $j)
		{
			if ($i == 'parent')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $p_id = $v;
						if ($k == 'caption') $p_caption = $v;						
					}
					if ($p_id == $id) continue;
					if ($p_id == $parent_id)
						$main_option = Array('value' => $p_id, 'caption' => $p_caption .' (id='. $p_id .')', 'selected' => 'selected');
					else
						$main_option = Array('value' => $p_id, 'caption' => $p_caption .' (id='. $p_id .')');
					$main_options[] = $main_option;
				}
			}
		}
		$form_data = Array(
						Array('type' => 'select', 'id' => 'parent_id', 'name' => 'parent_id', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Rodzic', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// dostępność:
		
		$sel = Array('', '', '', '');
		if ($permission == 1) $sel[0] = 'selected';
		else if ($permission == 2) $sel[1] = 'selected';
		else if ($permission == 3) $sel[2] = 'selected';
		else $sel[3] = 'selected';

		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => '1 (administratorzy)', $sel[0] => $sel[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => '2 (operatorzy)', $sel[1] => $sel[1]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => '3 (użytkownicy)', $sel[2] => $sel[2]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '4', 'caption' => '4 (wszyscy, goście)', $sel[3] => $sel[3]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'permission', 'name' => 'permission', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Dostępność (dla grup użytkowników)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// kolejność:
		
		$p_order = 0;

		$main_options = Array();
		
		foreach ($import as $i => $j)
		{
			if ($i == 'order')
			{
				foreach ($j as $key => $value)
				{
					$p_order++;					
					if ($p_order == $item_order)
						$main_option = Array('value' => $p_order, 'caption' => 'Pozycja '. $p_order .'.', 'selected' => 'selected');
					else
						$main_option = Array('value' => $p_order, 'caption' => 'Pozycja '. $p_order .'.');
					$main_options[] = $main_option;
				}
			}
		}
		if (empty($main_options))
		{
			$p_order++;					
			$main_option = Array('value' => $p_order, 'caption' => 'Pozycja '. $p_order .'.');
			$main_options[] = $main_option;
		}
		$form_data = Array(
						Array('type' => 'select', 'id' => 'item_order', 'name' => 'item_order', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Kolejność (położenie na liście)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// tekst:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'caption', 'name' => 'caption', 'caption' => '', 'value' => $caption, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Tekst (menu)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// adres:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'link', 'name' => 'link', 'caption' => '', 'value' => $link, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Adres (link)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		$actv = Array('', '');
		if ($visible == 1) $actv[0] = 'selected';
		else $actv[1] = 'selected';
		
		// aktywna:
		
		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => 'tak', $actv[0] => $actv[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '0', 'caption' => 'nie', $actv[1] => $actv[1]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'visible', 'name' => 'visible', 'option' => $main_options, 'style' => 'width: 100px;', 'description' => '(czy kategoria ma być widoczna w serwisie)')
						);
		$form_input = Array('caption' => 'Aktywna', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// nowe okno:
		
		$form_data = Array(
						Array('type' => 'checkbox', 'id' => 'target', 'name' => 'target', 'caption' => 'Otwieranie w osobnym oknie', $target_chkd => $target_chkd, 'onclick' => '')
						);
		$form_input = Array('caption' => 'Nowe okno', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		if (is_array($row))
		{
			// modyfikacja:
			
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $modified, 'style' => '')
							);
			$form_input = Array('caption' => 'Modyfikacja', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'level', 'name' => 'level', 'value' => $level),
						Array('type' => 'hidden', 'id' => 'icon_id', 'name' => 'icon_id', 'value' => $icon_id),
						Array('type' => 'hidden', 'id' => 'page_id', 'name' => 'page_id', 'value' => $page_id),
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
		
		$mode = isset($_SESSION['mode']) ? $_SESSION['mode'] : NULL;
		
		switch ($mode)
		{
			case 1:
				$list_title = strtoupper(MODULE_NAME) . ' - Pasek nawigacji';
				break;
			case 2:
				$list_title = strtoupper(MODULE_NAME) . ' - Menu boczne';
				break;
			default:
				$list_title = strtoupper(MODULE_NAME) . ' - Wszystkie';
				break;
		}
		$list_image = 'img/32x32/document-library.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);

		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '8%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'move-up', 'icon' => 'move_up.png', 'title' => 'Wyżej'),
			array('action' => 'move-down', 'icon' => 'move_down.png', 'title' => 'Niżej'),
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