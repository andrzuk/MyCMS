<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Visitors_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $failed)
	{
		$id = NULL;
		$period_from = NULL;
		$period_to = NULL;
		$condition_field = NULL;
		$condition_operator = NULL; 
		$condition_value = NULL;
		$addition_field = NULL;
		$addition_operator = NULL; 
		$addition_value = NULL;
		$exceptions = NULL;
		$modified = NULL;
		
		if (is_array($row))
		{
			$id = $row['id'];
			$period_from = $row['period_from'];
			$period_to = $row['period_to'];
			$condition_field = $row['condition_field'];
			$condition_operator = $row['condition_operator']; 
			$condition_value = $row['condition_value'];
			$addition_field = $row['addition_field'];
			$addition_operator = $row['addition_operator']; 
			$addition_value = $row['addition_value'];
			$exceptions = $row['exceptions'];
			$modified = $row['modified'];
		}
		
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Szukaj';
		$form_image = 'img/32x32/search.png';
		$form_width = '100%';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php?route=' . MODULE_NAME;
		
		$main_form->set_action($form_action);

		// failed:
		
		$main_form->set_failed($failed);

		// okres:
		
		$main_cell = '<div id="multi">';
			
		$period_from_year = intval(substr($period_from, 0, 4));
		$period_from_month = intval(substr($period_from, 5, 2));
		$period_from_day = intval(substr($period_from, 8, 2));

		$period_to_year = intval(substr($period_to, 0, 4));
		$period_to_month = intval(substr($period_to, 5, 2));
		$period_to_day = intval(substr($period_to, 8, 2));
		
		$main_cell .= '<select name="period_from_year" class="FormComboBox">';
		for ($i = 2013; $i <= 2031; $i++) $main_cell .= $i == $period_from_year ? '<option selected="selected">'.$i.'</option>' : '<option>'.$i.'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_from_month" class="FormComboBox">';
		for ($i = 1; $i <= 12; $i++) $main_cell .= $i == $period_from_month ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_from_day" class="FormComboBox">';
		for ($i = 1; $i <= 31; $i++) $main_cell .= $i == $period_from_day ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select>';

		$main_cell .= '&nbsp; – &nbsp;';

		$main_cell .= '<select name="period_to_year" class="FormComboBox">';
		for ($i = 2013; $i <= 2031; $i++) $main_cell .= $i == $period_to_year ? '<option selected="selected">'.$i.'</option>' : '<option>'.$i.'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_to_month" class="FormComboBox">';
		for ($i = 1; $i <= 12; $i++) $main_cell .= $i == $period_to_month ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_to_day" class="FormComboBox">';
		for ($i = 1; $i <= 31; $i++) $main_cell .= $i == $period_to_day ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select>';
		
		$main_cell .= '</div>';

		$form_data = Array(
						Array('type' => 'label', 'id' => '', 'name' => '', 'caption' => '', 'value' => $main_cell, 'style' => 'width: 97%;')
						);
		$form_input = Array('caption' => 'Okres czasu', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// warunek:
		
		$sel = Array(NULL, NULL, NULL, NULL, NULL);
		if ($condition_field == 'id') $sel[0] = 'selected';
		if ($condition_field == 'visitor_ip') $sel[1] = 'selected';
		if ($condition_field == 'http_referer') $sel[2] = 'selected';
		if ($condition_field == 'request_uri') $sel[3] = 'selected';
		if ($condition_field == 'visited') $sel[4] = 'selected';

		$main_options_field = Array();	
		$main_option = Array('value' => '', 'caption' => '(brak)');
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'id', 'caption' => 'id', $sel[0] => $sel[0]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'visitor_ip', 'caption' => 'visitor_ip', $sel[1] => $sel[1]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'http_referer', 'caption' => 'http_referer', $sel[2] => $sel[2]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'request_uri', 'caption' => 'request_uri', $sel[3] => $sel[3]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'visited', 'caption' => 'visited', $sel[4] => $sel[4]);
		$main_options_field[] = $main_option;

		$op = Array(NULL, NULL, NULL, NULL, NULL, NULL);
		if ($condition_operator == '1') $op[0] = 'selected';
		if ($condition_operator == '2') $op[1] = 'selected';
		if ($condition_operator == '3') $op[2] = 'selected';
		if ($condition_operator == '4') $op[3] = 'selected';
		if ($condition_operator == '5') $op[4] = 'selected';
		if ($condition_operator == '6') $op[5] = 'selected';

		$main_options_operator = Array();
		$main_option = Array('value' => '0', 'caption' => '(brak)');
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '1', 'caption' => 'równy (=)', $op[0] => $op[0]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'like (%)', $op[1] => $op[1]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'mniejszy (&lt;)', $op[2] => $op[2]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '4', 'caption' => 'większy (&gt;)', $op[3] => $op[3]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '5', 'caption' => 'od-do (between)', $op[4] => $op[4]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '6', 'caption' => 'różny (&lt;&gt;)', $op[5] => $op[5]);
		$main_options_operator[] = $main_option;

		$form_data = Array(
						Array('type' => 'select', 'id' => 'condition_field', 'name' => 'condition_field', 'option' => $main_options_field, 'description' => '', 'style' => 'width: 26%;'),
						Array('type' => 'select', 'id' => 'condition_operator', 'name' => 'condition_operator', 'option' => $main_options_operator, 'description' => '', 'style' => 'width: 26%;'),
						Array('type' => 'text', 'id' => 'condition_value', 'name' => 'condition_value', 'caption' => '', 'value' => $condition_value, 'style' => 'width: 40%;')
						);
		$form_input = Array('caption' => 'Warunek', 'data' => $form_data);
		$form_inputs[] = $form_input;
	
		// dodatkowo:

		$addsel = Array(NULL, NULL, NULL, NULL, NULL);
		if ($addition_field == 'id') $addsel[0] = 'selected';
		if ($addition_field == 'visitor_ip') $addsel[1] = 'selected';
		if ($addition_field == 'http_referer') $addsel[2] = 'selected';
		if ($addition_field == 'request_uri') $addsel[3] = 'selected';
		if ($addition_field == 'visited') $addsel[4] = 'selected';

		$main_options_field = Array();	
		$main_option = Array('value' => '', 'caption' => '(brak)');
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'id', 'caption' => 'id', $addsel[0] => $addsel[0]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'visitor_ip', 'caption' => 'visitor_ip', $addsel[1] => $addsel[1]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'http_referer', 'caption' => 'http_referer', $addsel[2] => $addsel[2]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'request_uri', 'caption' => 'request_uri', $addsel[3] => $addsel[3]);
		$main_options_field[] = $main_option;
		$main_option = Array('value' => 'visited', 'caption' => 'visited', $addsel[4] => $addsel[4]);
		$main_options_field[] = $main_option;

		$addop = Array(NULL, NULL, NULL, NULL, NULL, NULL);
		if ($addition_operator == '1') $addop[0] = 'selected';
		if ($addition_operator == '2') $addop[1] = 'selected';
		if ($addition_operator == '3') $addop[2] = 'selected';
		if ($addition_operator == '4') $addop[3] = 'selected';
		if ($addition_operator == '5') $addop[4] = 'selected';
		if ($addition_operator == '6') $addop[5] = 'selected';

		$main_options_operator = Array();
		$main_option = Array('value' => '0', 'caption' => '(brak)');
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '1', 'caption' => 'równy (=)', $addop[0] => $addop[0]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'like (%)', $addop[1] => $addop[1]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'mniejszy (&lt;)', $addop[2] => $addop[2]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '4', 'caption' => 'większy (&gt;)', $addop[3] => $addop[3]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '5', 'caption' => 'od-do (between)', $addop[4] => $addop[4]);
		$main_options_operator[] = $main_option;
		$main_option = Array('value' => '6', 'caption' => 'różny (&lt;&gt;)', $addop[5] => $addop[5]);
		$main_options_operator[] = $main_option;

		$form_data = Array(
						Array('type' => 'select', 'id' => 'addition_field', 'name' => 'addition_field', 'option' => $main_options_field, 'description' => '', 'style' => 'width: 26%;'),
						Array('type' => 'select', 'id' => 'addition_operator', 'name' => 'addition_operator', 'option' => $main_options_operator, 'description' => '', 'style' => 'width: 26%;'),
						Array('type' => 'text', 'id' => 'addition_value', 'name' => 'addition_value', 'caption' => '', 'value' => $addition_value, 'style' => 'width: 40%;')
						);
		$form_input = Array('caption' => 'Dodatkowo', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// wykluczenia:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'exceptions', 'name' => 'exceptions', 'caption' => '', 'value' => $exceptions, 'style' => 'width: 95%;')
						);
		$form_input = Array('caption' => 'Wykluczenia', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// modyfikacja:
		
		$form_data = Array(
						Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $modified, 'style' => '')
						);
		$form_input = Array('caption' => 'Modyfikacja', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// inputs:
	
		$main_form->set_inputs($form_inputs);

		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'run_button', 'name' => 'run_button', 'value' => 'Pokaż', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content = $main_form->build_form();
		
		$site_content .= '<div style="margin: 10px;"></div>';
		
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
		
		$list_title = 'Znalezione pozycje';
		$list_image = 'img/32x32/globe-blue.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '30%', 'align' => 'left', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
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
		$view_width = '100%';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$main_view->set_row($row);
		
		$main_view->set_columns($columns);
		
		$main_view->set_buttons(array('cancel',));

		// render:
		
		$site_content = $main_view->build_view();
		
		// View Generator.
		
		return $site_content;
	}
}

?>