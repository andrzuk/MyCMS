<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Rejectors_View
{
	public function __construct($db)
	{
	}
	
	/*
	 * Komponenty
	 */
	
	public function ShowComponents($left_panel, $right_panel)
	{
		$contents = '';
		
		$contents .= '<table>';
		$contents .= '<tr>';
		$contents .= '<td width="50%" style="vertical-align: top;">';
		$contents .= $left_panel;
		$contents .= '</td>';
		$contents .= '<td width="50%" style="vertical-align: top;">';
		$contents .= $right_panel;
		$contents .= '</td>';
		$contents .= '<tr>';
		$contents .= '</table>';
		
		return $contents;
	}
	 
	/*
	 * Lista
	 */
	 
	public function ShowList($list, $columns, $params)
	{
		// List Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'list.php';
		
		$main_list = new ListBuilder();
		
		$list_title = 'Odrzucone żądania';
		$list_image = 'img/32x32/logout.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '40%', 'align' => 'left', 'visible' => '1'),
			array('width' => '30%', 'align' => 'left', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
				
		// dostępne akcje:
		$col_actions = array();
		
		$main_list->set_actions($col_actions);

		// dostępne ustawianie daty:
		$main_list->set_dates(TRUE);

		// render:
		
		$site_content = $main_list->build_list();
		
		// List Generator.
		
		return $site_content;
	}
	
	/*
	 * Wykres
	 */
	
	public function ShowSummaryChart($row)
	{
		// Chart Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'chart.php';
		
		$main_chart = new ChartBuilder();
		
		$chart_title = 'Statystyka odrzuceń';
		
		$chart_image = 'img/32x32/chart_line.png';

		$main_chart->init($chart_title, $chart_image);

		$main_chart->set_module(MODULE_NAME);
		
		$main_chart->set_data($row);
		
		$main_chart->set_params(NULL);
		
		$main_chart->set_import(NULL);
			
		// dostępne ustawianie daty:
		$main_chart->set_dates(FALSE);

		// render:
		
		$site_content = $main_chart->build_summary_chart();
		
		// Chart Generator.
		
		return $site_content;		
	}	
}

?>

