<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Sites_View
{
	private $setting;
	
	public function __construct($db)
	{
		$this->setting = new Settings($db);
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$id = 0;
		$main_page = 0;
		$system_page = isset($_SESSION['form_fields']['system_page']) ? $_SESSION['form_fields']['system_page'] : 1;
		$category_id = isset($_SESSION['form_fields']['category_id']) ? $_SESSION['form_fields']['category_id'] : 0;
		$title = isset($_SESSION['form_fields']['title']) ? $_SESSION['form_fields']['title'] : NULL;
		$contents = isset($_SESSION['form_fields']['contents']) ? $_SESSION['form_fields']['contents'] : NULL;
		$author_id = isset($_SESSION['form_fields']['author_id']) ? $_SESSION['form_fields']['author_id'] : 0;
		$visible = isset($_SESSION['form_fields']['visible']) ? $_SESSION['form_fields']['visible'] : 1;
		$modified = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$main_page = $row['main_page'];
			$system_page = $row['system_page'];
			$category_id = $row['category_id'];
			$title = $row['title'];
			$contents = $row['contents']; 
			$author_id = $row['author_id'];
			$visible = $row['visible'];
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
			$form_title = 'Edycja strony';
		}
		else
		{
			$form_title = 'Nowa strona';
		}
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '100%';
		$form_widths = Array('10%', '90%');
		
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
			if ($i == 'authors')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $user_id = $v;
						if ($k == 'user_login') $user_login = $v;						
					}
					if ($user_id == $author_id) $author_login = $user_login;
				}
			}
		}		
			
		// required:
		
		$main_form->set_required($required);
		
		// failed:
		
		$main_form->set_failed($failed);

		// odczytuje z konfiguracji aktywność edytora CkEdit:
		$using_office_editor = $this->setting->get_config_key('using_office_editor');
		
		// CkEditor:
		
		$main_form->set_editor($using_office_editor);
		
		// odczytuje z konfiguracji aktywność podświetlania składni CodePress:
		$using_codepress_editor = $this->setting->get_config_key('using_codepress_editor');
		
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
		
		// tytuł:

		$form_data = Array(
						Array('type' => 'text', 'id' => 'title', 'name' => 'title', 'caption' => '', 'value' => $title, 'style' => 'width: 99%;')
						);
		$form_input = Array('caption' => 'Tytuł', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// główna:
		
		$sel = array('0' => '', '1' => '', '2' => '');
		$sel[$main_page] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => '0', 'caption' => 'normalna strona systemowa serwisu', $sel['0'] => $sel['0']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '1', 'caption' => 'strona główna - startowa serwisu (index)', $sel['1'] => $sel['1']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'strona kontaktu z serwisem (contact)', $sel['2'] => $sel['2']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'main_page', 'name' => 'main_page', 'option' => $main_options, 'description' => '', 'style' => 'width: 99%;')
						);
		$form_input = Array('caption' => 'Typ strony', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// treść:

		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'contents', 'name' => 'contents', 'value' => $contents, 'style' => 'height: 500px; width: 99%; min-width: 800px; min-height: 400px;')
						);
		$form_input = Array('caption' => 'Treść', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// aktywna:
		
		$actv = Array('', '');
		if ($visible == 1) $actv[0] = 'selected';
		else $actv[1] = 'selected';
		
		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => 'tak', $actv[0] => $actv[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '0', 'caption' => 'nie', $actv[1] => $actv[1]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'visible', 'name' => 'visible', 'option' => $main_options, 'style' => 'width: 100px;', 'description' => '(czy strona ma być widoczna w serwisie)')
						);
		$form_input = Array('caption' => 'Aktywna', 'data' => $form_data);
		$form_inputs[] = $form_input;

		if (is_array($row))
		{
			// modyfikacja:
			
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $modified, 'style' => ''),
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => ' ('.$author_login.')', 'style' => ''),
							);
			$form_input = Array('caption' => 'Modyfikacja', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'system_page', 'name' => 'system_page', 'value' => $system_page),
						Array('type' => 'hidden', 'id' => 'author_id', 'name' => 'author_id', 'value' => $author_id),
						Array('type' => 'hidden', 'id' => 'category_id', 'name' => 'category_id', 'value' => $category_id),
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
		$list_image = 'img/32x32/page_world.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);

		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1', 'icon' => '1'),
			array('width' => '20%', 'align' => 'left', 'visible' => '1'),
			array('width' => '35%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '0%', 'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
			array('action' => 'edit', 'icon' => 'edit.png', 'title' => 'Edytuj'),
			array('action' => 'archive', 'icon' => 'archives.png', 'title' => 'Archiwizuj'),
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
		
		array_push($columns, array('db_name' => 'archives', 'column_name' => 'Archiwa', 'sorting' => 0));
		
		$main_view->set_columns($columns);

		$main_view->set_buttons(array('edit', 'cancel',));

		// render:
		
		$site_content = $main_view->build_view();
		
		// View Generator.
		
		return $site_content;
	}
	
	/*
	 * Podgląd
	 */
	
	public function PreviewArchive($row)
	{
		$site_content = NULL;
		
		$site_content .= '<div class="PageMainContent">';
		
		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'contents') $site_content .= $value;
			}
		}
		
		$site_content .= '</div>';
		
		$site_content = empty($row) ? NULL : $site_content;
		
		return $site_content;
	}
}

?>