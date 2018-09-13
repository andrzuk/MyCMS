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
	 
	public function ShowForm($rows, $failed)
	{
		$period_from = NULL;
		$period_to = NULL;
		$filters = array();
		$exceptions = NULL;
		$modified = NULL;
		
		if (is_array($rows))
		{
			foreach ($rows as $k => $v)
			{
				foreach ($v as $key => $val)
				{
					if ($key == 'field') $field = $val;
					if ($key == 'operator') $operator = $val;
					if ($key == 'value') $value = $val;
				}
				if ($field == 'period_from') $period_from = $value;
				if ($field == 'period_to') $period_to = $value;
				if ($field == 'exceptions') $exceptions = $value;
				if ($field == 'modified') $modified = $value;
				if (in_array($field, array('id', 'visitor_ip', 'http_referer', 'request_uri', 'visited')))
				{
					$filters[] = array('field' => $field, 'operator' => $operator, 'value' => $value);
				}
			}
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
		$form_widths = Array('15%', '85%');
		
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
		
		$main_cell .= '<select name="period_from_year" class="FormComboBox year">';
		for ($i = 2013; $i <= 2031; $i++) $main_cell .= $i == $period_from_year ? '<option selected="selected">'.$i.'</option>' : '<option>'.$i.'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_from_month" class="FormComboBox month">';
		for ($i = 1; $i <= 12; $i++) $main_cell .= $i == $period_from_month ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_from_day" class="FormComboBox day">';
		for ($i = 1; $i <= 31; $i++) $main_cell .= $i == $period_from_day ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select>';

		$main_cell .= '&nbsp; – &nbsp;';

		$main_cell .= '<select name="period_to_year" class="FormComboBox year">';
		for ($i = 2013; $i <= 2031; $i++) $main_cell .= $i == $period_to_year ? '<option selected="selected">'.$i.'</option>' : '<option>'.$i.'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_to_month" class="FormComboBox month">';
		for ($i = 1; $i <= 12; $i++) $main_cell .= $i == $period_to_month ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select> ';

		$main_cell .= '<select name="period_to_day" class="FormComboBox day">';
		for ($i = 1; $i <= 31; $i++) $main_cell .= $i == $period_to_day ? '<option selected="selected">'.sprintf("%02d", $i).'</option>' : '<option>'.sprintf("%02d", $i).'</option>';
		$main_cell .= '</select>';
		
		$main_cell .= '<span id="modified" style="float: right; padding: 5px;">Modyfikacja: ' . $modified . '</span>';
		
		$main_cell .= '</div>';

		$form_data = Array(
						Array('type' => 'label', 'id' => '', 'name' => '', 'caption' => '', 'value' => $main_cell, 'style' => 'width: 100%; padding: 15px 5px;')
						);
		$form_input = Array('caption' => 'Okres czasu', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// dynamiczny warunek:
		
		$filters_controls = NULL;
		foreach ($filters as $k => $v)
		{
			$filters_controls .= '
				<div class="condition-item">
					<select name="field-'.$k.'">
						<option value="id" '. ($v['field'] == 'id' ? 'selected' : NULL) .'>id</option>
						<option value="visitor_ip" '. ($v['field'] == 'visitor_ip' ? 'selected' : NULL) .'>visitor_ip</option>
						<option value="http_referer" '. ($v['field'] == 'http_referer' ? 'selected' : NULL) .'>http_referer</option>
						<option value="request_uri" '. ($v['field'] == 'request_uri' ? 'selected' : NULL) .'>request_uri</option>
						<option value="visited" '. ($v['field'] == 'visited' ? 'selected' : NULL) .'>visited</option>
					</select><select name="operator-'.$k.'">
						<option value="equal" '. ($v['operator'] == 'equal' ? 'selected' : NULL) .'>równy (=)</option>
						<option value="like" '. ($v['operator'] == 'like' ? 'selected' : NULL) .'>like (%)</option>
						<option value="less" '. ($v['operator'] == 'less' ? 'selected' : NULL) .'>mniejszy (<)</option>
						<option value="great" '. ($v['operator'] == 'great' ? 'selected' : NULL) .'>większy (>)</option>
						<option value="between" '. ($v['operator'] == 'between' ? 'selected' : NULL) .'>od-do (between)</option>
						<option value="differ" '. ($v['operator'] == 'differ' ? 'selected' : NULL) .'>różny (<>)</option>
					</select><input name="value-'.$k.'" type="text" value="'.$v['value'].'"><button onclick="removeCondition(this, event)">Usuń</button>
				</div>
			';
		}
		$main_cell = '
			<div id="filters">' . $filters_controls . '</div>
			<div id="add-button">
				<button onclick="addCondition(event)">Dodaj warunek...</button>
			</div>
			<script>
				var idx = document.getElementById("filters").childNodes.length;
				function addCondition(event) { 
					idx++;
					event.preventDefault();
					var div = document.createElement("div");
					div.setAttribute("class", "condition-item");
					var selectField = document.createElement("select");
					selectField.setAttribute("name", "field-" + idx.toString());
					var optionsField = [{ text: "id", value: "id" }, { text: "visitor_ip", value: "visitor_ip" }, { text: "http_referer", value: "http_referer" }, { text: "request_uri", value: "request_uri" }, { text: "visited", value: "visited" }];
					for (var i = 0; i < optionsField.length; i++) {
						var option = document.createElement("option");
						option.text = optionsField[i].text;
						option.value = optionsField[i].value;
						selectField.add(option);
					}
					div.appendChild(selectField);
					var selectOperator = document.createElement("select");
					selectOperator.setAttribute("name", "operator-" + idx.toString());
					var optionsOperator = [{ text: "równy (=)", value: "equal" }, { text: "like (%)", value: "like" }, { text: "mniejszy (<)", value: "less" }, { text: "większy (>)", value: "great" }, { text: "od-do (between)", value: "between" }, { text: "różny (<>)", value: "differ" }];
					for (var i = 0; i < optionsOperator.length; i++) {
						var option = document.createElement("option");
						option.text = optionsOperator[i].text;
						option.value = optionsOperator[i].value;
						selectOperator.add(option);
					}
					div.appendChild(selectOperator);
					var conditionValue = document.createElement("input");
					conditionValue.type = "text";
					conditionValue.setAttribute("name", "value-" + idx.toString());
					div.appendChild(conditionValue);
					var removeCondition = document.createElement("button");
					removeCondition.innerHTML = "Usuń";
					removeCondition.setAttribute("onclick", "removeCondition(this, event)");
					div.appendChild(removeCondition);
					document.getElementById("filters").appendChild(div);
				}
				function removeCondition(owner, event) {
					event.preventDefault();
					owner.parentNode.remove();
				}
			</script>
		';
		
		$form_data = Array(
						Array('type' => 'label', 'id' => '', 'name' => '', 'caption' => '', 'value' => $main_cell, 'style' => 'width: 100%;')
						);
		$form_input = Array('caption' => 'Filtr - warunek <br>(typu AND)', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		// wykluczenia:
		
		$form_data = Array(
						Array('type' => 'textarea', 'id' => 'exceptions', 'name' => 'exceptions', 'value' => $exceptions, 'style' => '')
						);
		$form_input = Array('caption' => 'Wykluczenia', 'data' => $form_data);
		$form_inputs[] = $form_input;

		// inputs:
	
		$main_form->set_inputs($form_inputs);

		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'run_button', 'name' => 'run_button', 'value' => 'Pokaż', 'style' => 'width: 100px;', 'onclick' => '');
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