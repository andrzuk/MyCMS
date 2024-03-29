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
	
	public function ShowComponents($left_panel, $right_panel, $below_panel)
	{
		$contents = '';
		
		$contents .= '<table>';
		$contents .= '<tr>';
		$contents .= '<td width="50%" style="vertical-align: top;">';
		$contents .= $left_panel;
		$contents .= '</td>';
		$contents .= '<td width="50%" style="vertical-align: top;">';
		$contents .= $right_panel;
		$contents .= '<br>';
		$contents .= $below_panel;
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
		$list_image = 'img/32x32/firewall.png';

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
		$main_chart->set_dates(TRUE);

		// render:

		$chart_width = 650;
		$chart_height = 250;

		$site_content = $main_chart->build_summary_chart($chart_width, $chart_height);
		
		// Chart Generator.
		
		return $site_content;		
	}
	
	/*
	 * Raport
	 */
	
	public function ShowStatsReport($data)
	{
		$report = '';
		$addresses = array();
		$counters = array('today' => array(), 'range' => array(), 'total' => array());
		$summary = array('today' => 0, 'range' => 0, 'total' => 0);
		
		$report .= '<table class="Table" width="100%" cellpadding="5" cellspacing="1">';
		$report .= '<tr style="font-size: larger;">';
		$report .= '<th>Lp.</th><th>Adres IP</th><th>Dzisiejsze</th><th>Ostatnie</th><th>Wszystkie</th>';
		$report .= '</tr>';
		
		foreach ($data['total'] as $item)
		{
			$addresses[] = $item['visitor_ip'];
		}
		foreach ($data as $range_name => $range_stats)
		{
			foreach ($range_stats as $stats_item)
			{
				foreach ($stats_item as $key => $value)
				{
					if ($key == 'visitor_ip') $visitor_ip = $value;
					if ($key == 'counter') $counter = $value;
				}
				$index = array_search($visitor_ip, $addresses);
				$counters[$range_name][$index] = $counter;
				$summary[$range_name] += $counter;
			}
		}
		for ($idx = 0; $idx < count($addresses); $idx++)
		{
			$report .= '<tr class="DataRowBright">';
			$report .= '<td class="DataCell">' . strval($idx + 1) . '.</td>';
			$report .= '<td class="DataCell">' . $addresses[$idx] . '</td>';
			$report .= '<td class="DataCell">' . number_format($counters['today'][$idx], 0, ',', '.') . '</td>';
			$report .= '<td class="DataCell">' . number_format($counters['range'][$idx], 0, ',', '.') . '</td>';
			$report .= '<td class="DataCell">' . number_format($counters['total'][$idx], 0, ',', '.') . '</td>';
			$report .= '</tr>';
		}
		$report .= '<tr class="DataRow">';
		$report .= '<td class="DataCell"></td>';
		$report .= '<td class="DataCell"><b>Razem:</b></td>';
		$report .= '<td class="DataCell"><b>' . number_format($summary['today'], 0, ',', '.') . '</b></td>';
		$report .= '<td class="DataCell"><b>' . number_format($summary['range'], 0, ',', '.') . '</b></td>';
		$report .= '<td class="DataCell"><b>' . number_format($summary['total'], 0, ',', '.') . '</b></td>';
		$report .= '</tr>';

		$report .= '</table>';
		
		return $report;
	}
}

?>

