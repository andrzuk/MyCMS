<?php

/*
 * Obliczenia wskaźników stron dla list nawigatora
 */

class Navi
{
	private $display_rows;
	private $pointer_band;
	private $search_value;
	private $restrict;
	
	public function __construct($db)
	{
		$_SESSION['page_counter'] = isset($_SESSION['page_counter']) ? $_SESSION['page_counter'] : 0;
		$_SESSION['page_pointer'] = isset($_SESSION['page_pointer']) ? $_SESSION['page_pointer'] : 0;
		$_SESSION['starting_position'] = isset($_SESSION['starting_position']) ? $_SESSION['starting_position'] : 0;
		$_SESSION['result_capacity'] = isset($_SESSION['result_capacity']) ? $_SESSION['result_capacity'] : 0;

		$setting = new Settings($db);

		$this->display_rows = $setting->get_config_key('display_list_rows');
		$this->pointer_band = $setting->get_config_key('page_pointer_band');

		if (isset($_GET['page_rows']))
		{
			$_SESSION['page_list_rows'] = intval($_GET['page_rows']) > 0 ? intval($_GET['page_rows']) : 10;
			unset($_SESSION['keep_paginator']);
		}

		if (isset($_GET['user']) || isset($_GET['mode']))
		{
			unset($_SESSION['keep_paginator']);
		}
		
		if (isset($_SESSION['page_list_rows']))
		{
			$this->display_rows = $_SESSION['page_list_rows'];
		}
	}
	
	public function init($list_columns)
	{
		$display_rows = $this->display_rows;
		$pointer_band = $this->pointer_band;
		
		if (!isset($_SESSION['sort_field'])) 
		{
			$_SESSION['sort_field'] = 0;
		}
		if (!isset($_SESSION['sort_order'])) 
		{
			$_SESSION['sort_order'] = 1;
		}
		if (isset($_POST['ListSearchButton']) || isset($_POST['SetDatesButton']) || isset($_GET['confirm']))
		{
			unset($_SESSION['keep_paginator']);
		}
		
		$record_object = array();

		$db_fields = array();

		$db_fields[] = NULL;

		foreach ($list_columns as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 'db_name') $db_name = $v;
				if ($k == 'column_name') $column_name = $v;
				if ($k == 'sorting') $sorting = $v;
			}
			$record_object[] = $db_name;
			if (!empty($column_name)) $db_fields[] = $db_name;
		}

		if (isset($_GET['skip'])) // zmiana strony
		{
			switch ($_GET['skip'])
			{
				case 'first':
					$_SESSION['starting_position'] = 0;
					$_SESSION['page_pointer'] = 0;
					break;
				case 'prev':
					if ($_SESSION['starting_position'] - $display_rows >= 0)
					{
						$_SESSION['starting_position'] -= $display_rows;
						$_SESSION['page_pointer']--;
					}
					else
					{
						$_SESSION['starting_position'] = 0;
						$_SESSION['page_pointer'] = 0;
					}
					break;
				case 'next':
					if ($_SESSION['starting_position'] + $display_rows < $_SESSION['result_capacity'])
					{
						$_SESSION['starting_position'] += $display_rows;
						$_SESSION['page_pointer']++;
					}
					break;
				case 'last':
					if ($_SESSION['result_capacity'] >= $display_rows)
					{
						$_SESSION['starting_position'] = $_SESSION['result_capacity'] - $display_rows;
						$_SESSION['page_pointer'] = $_SESSION['page_counter'] - 1;
					}
					break;
				default:
					break;
			}
		}
		else if (isset($_GET['page'])) // zmiana strony - idz do numeru
		{
			if (intval($_GET['page']) > 0 && intval($_GET['page']) <= $_SESSION['page_counter'])
			{
				$_SESSION['starting_position'] = $display_rows * intval($_GET['page'] - 1);
				$_SESSION['page_pointer'] = intval($_GET['page'] - 1);
			}
		}
		else // pierwsze uruchomienie
		{
			if (!isset($_SESSION['keep_paginator']))
			{
				$_SESSION['starting_position'] = 0;
				$_SESSION['page_pointer'] = 0;
			}
			
			if (isset($_GET['sort']))
			{
				if ($_GET['sort'] == $_SESSION['sort_field']) // to samo pole sortowania
					$_SESSION['sort_order'] = 1 - intval($_GET['order']);
				else // zmieniono pole sortowania
					$_SESSION['sort_order'] = intval($_GET['order']);
			}
		}
		if (isset($_GET['sort']))
		{
			$_SESSION['sort_field'] = $_GET['sort'];
		}

		$starting = $_SESSION['starting_position'];

		$list_params = array(
			'show_rows' => $display_rows,
			'sort_column' => $_SESSION['sort_field'],
			'sort_order' => $_SESSION['sort_order'],
			'show_page' => $_SESSION['page_pointer'],
			'page_counter' => $_SESSION['page_counter'],
			'page_band' => $pointer_band
		);

		$field_no = $_SESSION['sort_field'] > 0 && $_SESSION['sort_field'] <= sizeof($db_fields) ? $_SESSION['sort_field'] : 1;

		$field_no = $field_no < sizeof($db_fields) ? $field_no : 1;

		$field = $db_fields[$field_no];

		$order = $_SESSION['sort_order'] ? 'ASC' : 'DESC';

		$db_params = array(
			'sort_field' => $field,
			'sort_order' => $order,
			'start_from' => $starting,
			'show_rows' => $display_rows
		);
		
		return array(
			'record_object' => $record_object,
			'db_params' => $db_params,
			'list_params' => $list_params, 
			);
	}
	
	public function set_value($value)
	{
		$this->search_value = $value;
	}
	
	public function set_restrict($restrict)
	{
		$this->restrict = $restrict;
	}
	
	public function update($model_object, &$list_params)
	{
		// ograniczenia dla wartości:
		$restrict_value = isset($this->search_value) ? $this->search_value : NULL;
		
		// gdy wpisano filtr:
		$restrict_value = isset($_SESSION['list_filter']) ? $_SESSION['list_filter'] : $restrict_value;

		// ograniczenia dla usera:
		$restrict_id = isset($this->restrict) ? $this->restrict : NULL;

		// oblicza paginację:
		$model_object->SetPages($restrict_value, $restrict_id, $this->display_rows);

		// aktualizuje liczbę stron:
		$list_params['page_counter'] = $_SESSION['page_counter'];
	}
}

?>