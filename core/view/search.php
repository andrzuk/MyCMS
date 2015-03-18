<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Search_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Lista
	 */
	 
	public function ShowFound($list, $columns, $params)
	{
		// List Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'list.php';
		
		$main_list = new ListBuilder();
		
		$list_title = 'Znalezione pozycje';
		$list_image = 'img/32x32/search.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);

		// kolumny wyświetlane:
		$col_attrib = array(
			array('style' => 'font-size: medium; font-weight: bold; padding: 10px 0px 10px 0px; color: #900;', 'visible' => '1'),
			array('style' => 'font-size: small; color: #666;', 'visible' => '1'),
			array('style' => NULL, 'visible' => '0'),
			array('style' => NULL, 'visible' => '0'),
			array('style' => NULL, 'visible' => '0'),
			array('style' => 'font-size: 11px; color: #999; text-align: right;', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array();
		
		$main_list->set_actions($col_actions);

		// render:
		
		$site_content = $main_list->build_found_list();
		
		// List Generator.

		return $site_content;
	}
}

?>