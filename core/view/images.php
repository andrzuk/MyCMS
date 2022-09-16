<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Images_View
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
		$owner_id = 0;
		$picture_description = isset($_SESSION['form_fields']['picture_description']) ? $_SESSION['form_fields']['picture_description'] : NULL;
		$section_id = 1;
		$active = 1; 
		$modified = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$owner_id = $row['owner_id'];
			$file_name = $row['file_name'];
			$file_size = $row['file_size'];
			$picture_width = $row['picture_width'];
			$picture_height = $row['picture_height'];
			$picture_description = $row['picture_description'];
			$section_id = $row['section_id'];
			$active = $row['active']; 
			$modified = $row['modified'];
		}
		
		$file_format = NULL;
		
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja obrazka';
		}
		else
		{
			$form_title = 'Nowy obrazek';
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
		
		// enctype:
		
		$main_form->set_enctype('multipart/form-data');
		
		// import:
		
		foreach ($import as $i => $j)
		{
			if ($i == 'owner')
			{
				$owner_id = $j;
			}
		}		
			
		// required:
		
		$main_form->set_required($required);

		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		if (is_array($row))
		{
			$main_cell = NULL;
			$main_cell .= '<table cellpadding="0" cellspacing="0" width="90%" align="right">';
			$main_cell .= '<tr>';
			$main_cell .= '<td style="text-align: right;">'. $file_name .'</td>';
			$main_cell .= '</tr>';
			$main_cell .= '</table>';
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $id, 'style' => ''),
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $main_cell, 'style' => '')
							);
			$form_input = Array('caption' => 'Id', 'data' => $form_data);
			$form_inputs[] = $form_input;

			$main_cell = NULL;
			$main_cell .= '<table cellpadding="0" cellspacing="0" width="50%" align="right">';
			$main_cell .= '<tr>';
			$main_cell .= '<td style="text-align: right;"> Rozmiar: '. number_format($file_size / 1024, 0, '', '.') .' KB'. '</td>';
			$main_cell .= '</tr>';
			$main_cell .= '</table>';
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $picture_width .' x '. $picture_height .' px', 'style' => ''),
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $main_cell, 'style' => '')
							);
			$form_input = Array('caption' => 'Wymiary', 'data' => $form_data);
			$form_inputs[] = $form_input;

			$main_cell = NULL;
			$main_cell .= '<a href="index.php?route=images&action=preview&id=' . $id . '" class="ActionIcon">';
			$main_cell .= '<img src="' . GALLERY_DIR . IMG_DIR . $id.'" alt="ico" width="300" height="200" style="border: 1px solid #ccc; padding: 1px;" />';
			$main_cell .= '</a>';
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $main_cell, 'style' => 'text-align: center; padding: 10px;')
							);
			$form_input = Array('caption' => 'Podgląd', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}

		// plik:
		
		$form_data = Array(
						Array('type' => 'file', 'id' => 'user_file', 'name' => 'user_file', 'size' => '44', 'style' => '')
						);
		$form_input = Array('caption' => 'Grafika', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// typ (sekcja):
		
		$sel = Array('', '', '');
		if ($section_id == 1) $sel[0] = 'selected';
		else if ($section_id == 2) $sel[1] = 'selected';
		else $sel[2] = 'selected';

		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => 'Grafiki na stronach', $sel[0] => $sel[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'Ikony i nawigacja', $sel[1] => $sel[1]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'Rysunki w dokumentach', $sel[2] => $sel[2]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'section_id', 'name' => 'section_id', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Rodzaj grafiki', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// opis:
		
		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'picture_description', 'name' => 'picture_description', 'value' => $picture_description, 'style' => 'height: 90px; width: 96%; min-width: 400px; min-height: 100px;')
						);

		$form_input = Array('caption' => 'Opis zdjęcia', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// aktywne:
		
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
		
		// modyfikacja:
		
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
						Array('type' => 'hidden', 'id' => 'owner_id', 'name' => 'owner_id', 'value' => $owner_id),
						Array('type' => 'hidden', 'id' => 'file_format', 'name' => 'file_format', 'value' => $file_format),
						);
		$form_hiddens[] = $form_data;

		$main_form->set_hiddens($form_hiddens);

		// buttons:
				
		if (is_array($row))
		{
			$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Zapisz', 'style' => 'width: 80px;');
			$form_buttons[] = $form_data;
			$form_data = Array('type' => 'submit', 'id' => 'update_button', 'name' => 'update_button', 'value' => 'Zamknij', 'style' => 'width: 80px;');
			$form_buttons[] = $form_data;
		}		
		else
		{
			$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Wyślij', 'style' => 'width: 80px;');
			$form_buttons[] = $form_data;
		}
		$form_data = Array('type' => 'submit', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content = $main_form->build_form();
		
		// Form Generator.
		
		return $site_content;
	}
	
	/*
	 * Formularz Multi-Upload
	 */
	 
	public function ShowFormMulti($row, $required, $failed, $import)
	{
		$id = 0;
		$owner_id = 0;
		$section_id = 1;
		$active = 1; 
		$modified = NULL;

		$file_format = NULL;
		$picture_description = NULL;
		
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Upload';
		$form_image = 'img/32x32/upload.png';
		$form_width = '600px';
		$form_widths = Array('20%', '80%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php?route=' . MODULE_NAME . '&action=add-multi';
		
		$main_form->set_action($form_action);
		
		// enctype:
		
		$main_form->set_enctype('multipart/form-data');
		
		// import:
		
		foreach ($import as $i => $j)
		{
			if ($i == 'owner')
			{
				$owner_id = $j;
			}
		}		
			
		// required:
		
		$main_form->set_required($required);

		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		// pliki:
		
		$form_data = Array(
						Array('type' => 'file', 'id' => 'upload_files', 'name' => 'upload_files[]', 'multiple' => '', 'onChange' => 'makeFileList();', 'size' => '44', 'style' => '')
						);
		$form_input = Array('caption' => 'Grafiki (wybór wielu)', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// lista wczytanych:
		
		$files_list = '<ul id="file_list"><li>(brak)</li></ul>';
		
		$form_data = Array(
						Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $files_list, 'style' => '')
						);
		$form_input = Array('caption' => 'Wybrane pliki', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// typ (sekcja):
		
		$sel = Array('', '', '');
		if ($section_id == 1) $sel[0] = 'selected';
		else if ($section_id == 2) $sel[1] = 'selected';
		else $sel[2] = 'selected';

		$main_options = Array();
		
		$main_option = Array('value' => '1', 'caption' => 'Grafiki na stronach', $sel[0] => $sel[0]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'Ikony i nawigacja', $sel[1] => $sel[1]);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'Rysunki w dokumentach', $sel[2] => $sel[2]);
		$main_options[] = $main_option;
		$form_data = Array(
						Array('type' => 'select', 'id' => 'section_id', 'name' => 'section_id', 'option' => $main_options, 'description' => '', 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Rodzaj grafiki', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// aktywne:
		
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
		
		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'owner_id', 'name' => 'owner_id', 'value' => $owner_id),
						Array('type' => 'hidden', 'id' => 'file_format', 'name' => 'file_format', 'value' => $file_format),
						Array('type' => 'hidden', 'id' => 'picture_description', 'name' => 'picture_description', 'value' => $picture_description),
						);
		$form_hiddens[] = $form_data;

		$main_form->set_hiddens($form_hiddens);

		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'upload_button', 'name' => 'upload_button', 'value' => 'Wyślij', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		$form_data = Array('type' => 'submit', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content = $main_form->build_form();
		
		// Form Generator.
		
		$js = NULL;
		
		$js .= '<script type="text/javascript">';
		$js .= 'function makeFileList() {';
		$js .= '	var input = document.getElementById("upload_files");';
		$js .= '	var ul = document.getElementById("file_list");';
		$js .= '	while (ul.hasChildNodes()) {';
		$js .= '		ul.removeChild(ul.firstChild);';
		$js .= '	}';
		$js .= '	for (var i = 0; i < input.files.length; i++) {';
		$js .= '		var li = document.createElement("li");';
		$js .= '		li.innerHTML = input.files[i].name;';
		$js .= '		ul.appendChild(li);';
		$js .= '	}';
		$js .= '	if(!ul.hasChildNodes()) {';
		$js .= '		var li = document.createElement("li");';
		$js .= '		li.innerHTML = "(brak)";';
		$js .= '		ul.appendChild(li);';
		$js .= '	}';
		$js .= '}';
		$js .= '</script>';
		
		return $site_content . $js;
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
				$list_title = strtoupper(MODULE_NAME) . ' - Grafiki stron';
				break;
			case 2:
				$list_title = strtoupper(MODULE_NAME) . ' - Ikony nawigacji';
				break;
			case 3:
				$list_title = strtoupper(MODULE_NAME) . ' - Rysunki dokumentów';
				break;
			default:
				$list_title = strtoupper(MODULE_NAME) . ' - Wszystkie';
				break;
		}
		$list_image = 'img/32x32/application_view_gallery.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);

		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '4%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'center', 'visible' => '1', 'image' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'right', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '0'),
			array('width' => '8%', 'align' => 'center', 'visible' => '1'),
			array('width' => '12%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
			array('action' => 'edit', 'icon' => 'edit.png', 'title' => 'Edytuj'),
			array('action' => 'download', 'icon' => 'save.png', 'title' => 'Pobierz'),
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

	/*
	 * Podgląd
	 */
	
	public function PreviewRecord($row, $columns)
	{
		$max_width = 900;
		$pic_width = $row['picture_width'];
		
		$image_width = $pic_width < $max_width ? $pic_width : $max_width;
		
		$site_content = NULL;
		
		$site_content .= '<p style="text-align: center;">';
		$site_content .= '<img src="'. GALLERY_DIR . IMG_DIR . $row['id'] .'" width="' . $image_width . '" style="border: 1px solid #ccc; padding: 1px;" />';
		$site_content .= '</p>';
		
		$site_content .= '<p style="text-align: center; font-size: 12px; color: #666;">';
		$site_content .= 'Zdjęcie ' . $row['id'] . '. "' . $row['picture_description'] . '"';
		$site_content .= '</p>';
		
		return $site_content;
	}
}

?>