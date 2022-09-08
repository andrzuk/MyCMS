<?php

/*
 * Klasa odpowiedzialna za tworzenie wykresów - Generator Wykresów
 */

class ChartBuilder
{
	private $title;
	private $image;
	
	private $module;
	
	private $data;
	private $params;
	private $import;
	private $dates;
	
	private $p_title;

	private $chart_width;
	private $chart_height;
	
	function __construct()
	{
	}
	
	public function init($chart_title, $chart_image)
	{
		$this->image = $chart_image;
		$this->title = $chart_title;
	}
	
	public function set_module($module)
	{
		$this->module = $module;
	}
	
	public function set_data($data)
	{
		$this->data = $data;
	}
	
	public function set_params($params)
	{
		$this->params = $params;
	}
	
	public function set_import($import)
	{
		$this->import = $import;
	}
	
	public function set_dates($dates)
	{
		$this->dates = $dates;
	}
	
	/*
	 * Wykresy podsumowania:
	 */
	 
	public function build_summary_chart()
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$this->chart_width = 650;
		$this->chart_height = 300;
		
		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';		
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<form action="index.php?route=' . $this->module . '" method="post">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['date_from'].'" class="FormInput" style="width: 125px;" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['date_to'].'" class="FormInput" style="width: 125px;" />&nbsp;';
			$main_text .= '<input type="submit" id="SetDatesButton" name="SetDatesButton" value="OK" style="width: 40px;" />';
			$main_text .= '</form>';
			$main_text .= '</span>';
			$main_text .= '<span class="UserSelLabel">';
			$main_text .= 'Data:';
			$main_text .= '</span>';
		}
		$main_text .= '</th>';
		$main_text .= '</tr>';

		$main_text .= '<tr>';
		$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';

		$canvas_id = 0;
		foreach ($this->data as $key => $value)
		{
			$canvas_id++;
			$counter = 0;
			$series_points = array();
			$label_points = array();

			if ($key == 'index') // dla żądania strony index
			{
				$this->p_title = 'Strona główna';

				foreach ($value as $k => $v)
				{
					foreach ($v as $kk => $vv)
					{
						if ($kk == 'visited') $visited = $vv;
						if ($kk == 'counter') $index_counter = $vv;
					}
					array_push($series_points, $index_counter);
					array_push($label_points, substr(str_replace('-', '.', $visited), 2, 8));
					$counter++;
				}
			}
			if ($key == 'contact') // dla żądania strony kontaktowej
			{
				$this->p_title = 'Strona kontaktowa';

				foreach ($value as $k => $v)
				{
					foreach ($v as $kk => $vv)
					{
						if ($kk == 'visited') $visited = $vv;
						if ($kk == 'counter') $contact_counter = $vv;
					}
					array_push($series_points, $contact_counter);
					array_push($label_points, substr(str_replace('-', '.', $visited), 2, 8));
					$counter++;
				}
			}
	
			if ($counter) // są wyniki
			{
				$main_text .= '<span style="display: inline-block; padding: 10px 0;">';
				$main_text .= '<label style="font-weight: bold;">'. $this->p_title .'</label><br>';
				$main_text .= '<canvas id="canvas-pointers-'. $canvas_id . '" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;"></canvas>';
				$main_text .= '</span>';
				$main_text .= '<script>generateChartSelectedPoints('. $canvas_id .', '. json_encode($label_points) .', '. json_encode($series_points) .', \'\', \'\');</script>';
			}
			else // brak wyników
			{
				$main_text .= '<span style="display: inline-block; padding: 1px;">';
				$main_text .= '<label style="font-weight: bold;">'. $this->p_title .'</label><br>';
				$main_text .= '<img src="gallery/charts/empty.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title.'" title="'.$this->p_title.': Brak danych" />';
				$main_text .= '</span>';
			}
		}
	
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}
}

?>
