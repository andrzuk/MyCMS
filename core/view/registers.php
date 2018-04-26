<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Registers_View
{
	public function __construct($db)
	{
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
				$list_title = strtoupper(MODULE_NAME) . ' - Przyjęte';
				break;
			case 2:
				$list_title = strtoupper(MODULE_NAME) . ' - Odrzucone';
				break;
			default:
				$list_title = strtoupper(MODULE_NAME) . ' - Wszystkie';
				break;
		}
		$list_image = 'img/32x32/list_checked.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '20%', 'align' => 'left', 'visible' => '0'),
			array('width' => '10%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'center', 'visible' => '0'),
			array('width' => '0%', 'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
		);
		
		$main_list->set_actions($col_actions);

		// dostępne ustawianie daty:
		$main_list->set_dates(TRUE);

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
		
		$main_view->set_buttons(array('cancel',));

		// render:
		
		$site_content = $main_view->build_view();
		
		// View Generator.
		
		return $site_content;
	}
}

?>