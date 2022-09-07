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
	
	private $p_title_standard;
	private $p_title_asa;
	private $p_chart_width;
	private $p_chart_height;
	private $p_points_limit;
	private $p_font_title;
	private $p_font_scale;
	private $p_elements;
	private $p_show_data_1;
	private $p_show_data_2;
	private $p_show_data_3;
	private $p_background;
	private $p_title;
	private $p_legend;

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
	
	private function load_parameters()
	{
		// wczytuje parametry:
		
		$d_width = array(); $d_height = array(); $d_limit = array(); $d_title = array(); $d_scale = array();
		
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'chart_options') // ustawienia domyślne
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($key == 'type') $kind = $value;
							if ($key == 'caption') $caption = $value;
							if ($key == 'data') $datalist = $value;
							if ($key == 'value') $setting = $value;
						}
						switch ($i)
						{
							case 0:
								$this->p_title_standard = $setting;
								break;
							case 1:
								$this->p_title_asa = $setting;
								break;
							case 2:
								if (isset($datalist[$setting])) { $this->p_chart_width = $datalist[$setting]; $d_width = $datalist; }
								break;
							case 3:
								if (isset($datalist[$setting])) { $this->p_chart_height = $datalist[$setting]; $d_height = $datalist; }
								break;
							case 4:
								if (isset($datalist[$setting])) { $this->p_points_limit = $datalist[$setting]; $d_limit = $datalist; }
								break;
							case 5:
								if (isset($datalist[$setting])) { $this->p_font_title = $datalist[$setting]; $d_title = $datalist; }
								break;
							case 6:
								if (isset($datalist[$setting])) { $this->p_font_scale = $datalist[$setting]; $d_scale = $datalist; }
								break;
							case 7:
								$this->p_elements[0] = in_array($setting, array(0, 2)) ? 1 : 0;
								$this->p_elements[1] = in_array($setting, array(1, 2)) ? 1 : 0;
								break;
							case 8:
								$this->p_show_data_1 = $setting;
								break;
							case 9:
								$this->p_show_data_2 = $setting;
								break;
							case 10:
								$this->p_show_data_3 = $setting;
								break;
							case 11:
								$this->p_background = $setting;
								break;
							case 12:
								$this->p_title = $setting;
								break;
							case 13:
								$this->p_legend = $setting;
								break;
						}
					}
				}				
				if ($k == 'chart_user_options') // ustawienia użytkownika
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $kk => $vv)
						{
							if ($kk == 'id') $r_id = $vv;
							if ($kk == 'user_id') $r_user_id = $vv;
							if ($kk == 'key_name') $r_key_name = $vv;
							if ($kk == 'key_value') $r_key_value = $vv;
						}
						switch ($i)
						{
							case 0:
								$this->p_title_standard = $r_key_value;
								break;
							case 1:
								$this->p_title_asa = $r_key_value;
								break;
							case 2:
								if (isset($d_width[$r_key_value])) $this->p_chart_width = $d_width[$r_key_value];
								break;
							case 3:
								if (isset($d_height[$r_key_value])) $this->p_chart_height = $d_height[$r_key_value];
								break;
							case 4:
								if (isset($d_limit[$r_key_value])) $this->p_points_limit = $d_limit[$r_key_value];
								break;
							case 5:
								if (isset($d_title[$r_key_value])) $this->p_font_title = $d_title[$r_key_value];
								break;
							case 6:
								if (isset($d_scale[$r_key_value])) $this->p_font_scale = $d_scale[$r_key_value];
								break;
							case 7:
								$this->p_elements[0] = in_array($r_key_value, array(0, 2)) ? 1 : 0;
								$this->p_elements[1] = in_array($r_key_value, array(1, 2)) ? 1 : 0;
								break;
							case 8:
								$this->p_show_data_1 = $r_key_value;
								break;
							case 9:
								$this->p_show_data_2 = $r_key_value;
								break;
							case 10:
								$this->p_show_data_3 = $r_key_value;
								break;
							case 11:
								$this->p_background = $r_key_value;
								break;
							case 12:
								$this->p_title = $r_key_value;
								break;
							case 13:
								$this->p_legend = $r_key_value;
								break;
						}
					}
				}
			}
		}
		
		$this->chart_width = isset($this->p_chart_width) ? $this->p_chart_width : 800;
		$this->chart_height = isset($this->p_chart_height) ? $this->p_chart_height : 300;
		
		if (isset($this->p_points_limit)) define ('MAX_POINTS', $this->p_points_limit);
		else define ('MAX_POINTS', 100);
	}
	
	/*
	 * Wykresy zestawów Standard i ASA:
	 */
	 
	public function build_sets_charts()
	{
		$main_text = NULL;
		
		$pic_idx = 0;
		$pic_name = NULL;
		$cols_count = 1;

		$selected_user = isset($_SESSION['select_user']) ? $_SESSION['select_user'] : NULL;
		$selected_set = isset($_SESSION['select_set']) ? $_SESSION['select_set'] : 1;
	
		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';		
		$main_text .= '<span class="FormSearch">';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="get">';
		$main_text .= '<input type="hidden" name="route" value="' . $this->module . '" />';
		$main_text .= '<input type="hidden" name="action" value="charts" />';
		$main_text .= '<select name="dataset" class="FormComboBox" onchange="submit()" style="width: 100px;">';
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'task_sets')
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($key == 'set_id') $set_id = $value;
							if ($key == 'set_name') $set_name = $value;
						}
						if ($set_id == $selected_set)
							$main_text .= '<option value="'.$set_id.'" selected="selected">'.$set_name.'</option>';
						else
							$main_text .= '<option value="'.$set_id.'">'.$set_name.'</option>';
					}
				}
			}
		}
		$main_text .= '</select>';
		$main_text .= '</form>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Zestaw:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelect">';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="get">';
		$main_text .= '<input type="hidden" name="route" value="' . $this->module . '" />';
		$main_text .= '<input type="hidden" name="action" value="charts" />';
		$main_text .= '<select name="user" class="FormComboBox" onchange="submit()" style="width: 100px;">';
		$main_text .= '<option value="0">(dowolny)</option>';
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'users_list')
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($key == 'id') $user_id = $value;
							if ($key == 'user_login') $user_login = $value;
						}
						if ($user_id == $selected_user)
							$main_text .= '<option value="'.$user_id.'" selected="selected">'.$user_login.'</option>';
						else
							$main_text .= '<option value="'.$user_id.'">'.$user_login.'</option>';
					}
				}
			}
		}
		$main_text .= '</select>';
		$main_text .= '</form>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Użytkownik:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= '<a href="index.php?route=results&action=options&charts=true" class="PathLink"><img src="img/16x16/options.png" class="TopLinkIcon" alt="options" title="Ustawienia" /></a>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= '<a href="index.php?route=results" class="PathLink"><img src="img/16x16/text_list.png" class="TopLinkIcon" alt="tasks" title="Wyniki" /></a>';
		$main_text .= '</span>';
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<form action="index.php?route=' . $this->module . '&action=charts&sets=true" method="post">';
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
				
		// wczytuje parametry:
		$this->load_parameters();

		// segreguje dane:
		
		$a_data = array();
		$data_item = array();
				
		if ($selected_set == 1) // zestaw Standard
		{
			// wczytuje wszystkie dane:
			
			foreach ($this->data as $k => $v) 
			{
				foreach ($v as $kk => $vv)
				{
					$packet = FALSE;
					
					if ($kk == 'user_id') $user_id = $vv;
					if ($kk == 'task_type') $task_type = mb_strtoupper($vv);
					if ($kk == 'csv_file') $csv_file = $vv;
					if ($kk == 'pct_ok') $pct_ok = $vv;
					if ($kk == 'trials') $trials = $vv;
					if ($kk == 'trials_failed') $trials_failed = $vv;
					if ($kk == 'trials_ok') $trials_ok = $vv;
					if ($kk == 'req_pct') $req_pct = $vv;
					if ($kk == 'vot_level') $vot_level = $vv;
					if ($kk == 'vot_sequence_len') $vot_sequence_len = $vv;
					if ($kk == 'isi') $isi = $vv;
					if ($kk == 'display') $display = $vv;
					if ($kk == 'ms_per_sound') $ms_per_sound = $vv;
					if ($kk == 'isi_exclude_wav') $isi_exclude_wav = $vv;
					if ($kk == 'non_devs_at_start') $non_devs_at_start = $vv;
					if ($kk == 'random_order') { $random_order = $vv; $packet = TRUE; }
					
					if ($packet) // skompletowane dane dla jednego pomiaru
					{
						$data_item = array(
							'trials_failed' => $trials_failed,
							'trials_ok' => $trials_ok,
							'trials' => $trials,
						);					
						array_push($a_data, array('user_id' => $user_id, 'task_type' => $task_type, 'data' => $data_item));
					}
				}
			}
			
			// ustala grupy:
			
			$task_types = array(
				array(
					'name' => 'MODUŁ TRENINGOWY',
					'modules' => array('M_0_T_1', 'M_0_T_1A', 'M_0_T_1B', 'M_0_T_2A', 'M_0_T_2B', 'M_0_T_3A', 'M_0_T_3B', 'M_0_T_4', 'M_0_T_5', 'M_0_T_6', 'M_0_T_7',),
				),
				array(
					'name' => 'WZORCE DŹWIĘKOWE',
					'modules' => array('M_1_T_1', 'M_1_T_3', 'M_1_T_2', 'M_1_T_6', 'M_1_T_5',),
				),
				array(
					'name' => 'SEKWENCJE DŹWIĘKÓW',
					'modules' => array('M_3_T_1',),
				),
				array(
					'name' => 'SKARBY I LABIRYNTY',
					'modules' => array('M_4_T_1', 'M_4_T_5', 'M_4_T_6', 'M_4_T_7',),
				),
				array(
					'name' => 'ZABAWY SŁOWNE',
					'modules' => array('M_5_T_3', 'M_5_T_2', 'M_5_T_4',),
				),
				array(
					'name' => 'BAJKI I OPOWIEŚCI',
					'modules' => array('M_6_T_1',),
				),
				array(
					'name' => 'REAKCJE ODROCZONE',
					'modules' => array('M_7_T_1',),
				),
			);
		
			// rozdziela dane na poszczególne grupy:

			foreach ($task_types as $k => $v)
			{
				$canvas_id = $k + 1;
				
				foreach ($v as $kk => $vv)
				{
					if ($kk == 'name') $task_name = $vv;
					if ($kk == 'modules') $task_modules = $vv;
				}

				$pic_idx++;
				$pic_name = $selected_user . '_' . $pic_idx;
				$counter = 0;
				$series_points_1 = array();
				$series_points_2 = array();
				$series_points_3 = array();
				$label_points = array();
				
				foreach ($a_data as $k => $v)
				{
					foreach ($v as $kk => $vv)
					{
						if ($kk == 'user_id') $user_id = $vv;
						if ($kk == 'task_type') $task_type = $vv;
						if ($kk == 'data')
						{
							foreach ($vv as $kkk => $vvv)
							{
								if ($kkk == 'csv_file') $csv_file = $vvv;
								if ($kkk == 'pct_ok') $pct_ok = $vvv;
								if ($kkk == 'trials') $trials = $vvv;
								if ($kkk == 'trials_failed') $trials_failed = $vvv;
								if ($kkk == 'trials_ok') $trials_ok = $vvv;
								if ($kkk == 'req_pct') $req_pct = $vvv;
								if ($kkk == 'vot_level') $vot_level = $vvv;
								if ($kkk == 'vot_sequence_len') $vot_sequence_len = $vvv;
								if ($kkk == 'isi') $isi = $vvv;
								if ($kkk == 'display') $display = $vvv;
								if ($kkk == 'ms_per_sound') $ms_per_sound = $vvv;
								if ($kkk == 'isi_exclude_wav') $isi_exclude_wav = $vvv;
								if ($kkk == 'non_devs_at_start') $non_devs_at_start = $vvv;
								if ($kkk == 'random_order') $random_order = $vvv;
							}
							
							if (in_array($task_type, $task_modules)) // wynik należy do iterowanej grupy
							{
								if ($this->p_show_data_1) array_push($series_points_1, $trials_failed);
								if ($this->p_show_data_2) array_push($series_points_2, $trials_ok);
								if ($this->p_show_data_3) array_push($series_points_3, $trials);
								$counter++;
								array_push($label_points, $counter);
							}					
						}
					}
				}
				
				if ($counter <= MAX_POINTS) // ilość danych nie przekracza ustalonej granicy
				{
					if ($counter) // są wyniki
					{
						$main_text .= '<span style="display: inline-block; padding: 1px;">';
						$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
						$main_text .= '<canvas id="canvas-pointers-'. $canvas_id . '" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;"></canvas>';
						$main_text .= '</span>';
						$main_text .= '<script>generateChartSelectedPoints('. $canvas_id . ', '. json_encode($label_points) . ', '. json_encode($series_points_1) . ', '. json_encode($series_points_2) . ', '. json_encode($series_points_3) . ');</script>';
					}
					else // brak wyników
					{
						$main_text .= '<span style="display: inline-block; padding: 1px;">';
						$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
						$main_text .= '<img src="gallery/charts/empty.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_standard.'" title="'.$task_name.': Brak danych" />';
						$main_text .= '</span>';
					}
				}
				else // za dużo danych
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
					$main_text .= '<img src="gallery/charts/full.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_standard.'" title="'.$task_name.': Limit danych" />';
					$main_text .= '</span>';
				}
			}
		}

		if ($selected_set == 2) // zestaw ASA
		{
			// wczytuje wszystkie dane:
			
			foreach ($this->data as $k => $v) 
			{
				foreach ($v as $kk => $vv)
				{
					$packet = FALSE;
					
					if ($kk == 'user_id') $user_id = $vv;
					if ($kk == 'task_type') $task_type = mb_strtoupper($vv);
					if ($kk == 'csv_file') $csv_file = $vv;
					if ($kk == 'isi_start') $isi_start = $vv;
					if ($kk == 'isi_end') $isi_end = $vv;
					if ($kk == 'num_sss') $num_sss = $vv;
					if ($kk == 'accuracy') $accuracy = $vv;
					if ($kk == 'isi_alg_1') $isi_alg_1 = $vv;
					if ($kk == 'isi_alg_2') $isi_alg_2 = $vv;
					if ($kk == 'threshold_trial_alg') $threshold_trial_alg = $vv;
					if ($kk == 'learning_trials') { $learning_trials = $vv; $packet = TRUE; }
					
					if ($packet) // skompletowane dane dla jednego pomiaru
					{
						$data_item = array(
							'isi_alg_1' => $isi_alg_1,
							'isi_alg_2' => $isi_alg_2,
							'learning_trials' => $learning_trials,
						);
						array_push($a_data, array('user_id' => $user_id, 'task_type' => $task_type, 'data' => $data_item));
					}
				}
			}
			
			// ustala grupy:
			
			$task_types = array(
				array(
					'name' => 'KLIKI',
					'modules' => array('M_2_T_1', 'M_2_T_4', 'M_2_T_6',),
				),
				array(
					'name' => 'TONY',
					'modules' => array('M_2_T_2',),
				),
				array(
					'name' => 'KR_DL',
					'modules' => array('M_2_T_3', 'M_2_T_5',),
				),
				array(
					'name' => 'DC',
					'modules' => array('M_8_T_1',),
				),
			);

			// rozdziela dane na poszczególne grupy:

			foreach ($task_types as $k => $v)
			{
				$canvas_id = $k + 1;
				
				foreach ($v as $kk => $vv)
				{
					if ($kk == 'name') $task_name = $vv;
					if ($kk == 'modules') $task_modules = $vv;
				}

				$pic_idx++;
				$pic_name = $selected_user . '_' . $pic_idx;
				$counter = 0;
				$series_points_1 = array();
				$series_points_2 = array();
				$series_points_3 = array();
				$label_points = array();
				
				foreach ($a_data as $k => $v)
				{
					foreach ($v as $kk => $vv)
					{
						if ($kk == 'user_id') $user_id = $vv;
						if ($kk == 'task_type') $task_type = $vv;
						if ($kk == 'data')
						{
							foreach ($vv as $kkk => $vvv)
							{
								if ($kkk == 'csv_file') $csv_file = $vvv;
								if ($kkk == 'isi_start') $isi_start = $vvv;
								if ($kkk == 'isi_end') $isi_end = $vvv;
								if ($kkk == 'num_sss') $num_sss = $vvv;
								if ($kkk == 'accuracy') $accuracy = $vvv;
								if ($kkk == 'isi_alg_1') $isi_alg_1 = $vvv;
								if ($kkk == 'isi_alg_2') $isi_alg_2 = $vvv;
								if ($kkk == 'threshold_trial_alg') $threshold_trial_alg = $vvv;
								if ($kkk == 'learning_trials') $learning_trials = $vvv;
							}
							
							if (in_array($task_type, $task_modules)) // wynik należy do iterowanej grupy
							{
								if ($this->p_show_data_1) array_push($series_points_1, $isi_alg_1);
								if ($this->p_show_data_2) array_push($series_points_2, $isi_alg_2);
								if ($this->p_show_data_3) array_push($series_points_3, $learning_trials);
								$counter++;
								array_push($label_points, $counter);
							}					
						}
					}
				}
				
				if ($counter <= MAX_POINTS) // ilość danych nie przekracza ustalonej granicy
				{
					if ($counter) // są wyniki
					{
						$main_text .= '<span style="display: inline-block; padding: 1px;">';
						$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
						$main_text .= '<canvas id="canvas-pointers-'. $canvas_id . '" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;"></canvas>';
						$main_text .= '</span>';
						$main_text .= '<script>generateChartSelectedPoints('. $canvas_id . ', '. json_encode($label_points) . ', '. json_encode($series_points_1) . ', '. json_encode($series_points_2) . ', '. json_encode($series_points_3) . ');</script>';
					}
					else // brak wyników
					{
						$main_text .= '<span style="display: inline-block; padding: 1px;">';
						$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
						$main_text .= '<img src="gallery/charts/empty.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_standard.'" title="'.$task_name.': Brak danych" />';
						$main_text .= '</span>';
					}
				}
				else // za dużo danych
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $task_name .'</label><br>';
					$main_text .= '<img src="gallery/charts/full.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_asa.'" title="'.$task_name.': Limit danych" />';
					$main_text .= '</span>';
				}
			}
		}

		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}
	
	/*
	 * Wykresy porównawcze ASA:
	 */
	 
	public function build_compare_charts()
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$selected_user = isset($_SESSION['select_user']) ? $_SESSION['select_user'] : NULL;
		$selected_task = isset($_SESSION['select_task']) ? $_SESSION['select_task'] : NULL;
	
		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';		
		$main_text .= '<span class="FormSearch">';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="get">';
		$main_text .= '<input type="hidden" name="route" value="' . $this->module . '" />';
		$main_text .= '<input type="hidden" name="action" value="charts" />';
		$main_text .= '<select name="task" class="FormComboBox" onchange="submit()" style="width: 100px;">';
		$main_text .= '<option value="all">(dowolny)</option>';
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'task_types')
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($value == $selected_task)
								$main_text .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
							else
								$main_text .= '<option value="'.$value.'">'.$value.'</option>';
						}
					}
				}
			}
		}
		$main_text .= '</select>';
		$main_text .= '</form>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Task:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelect">';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="get">';
		$main_text .= '<input type="hidden" name="route" value="' . $this->module . '" />';
		$main_text .= '<input type="hidden" name="action" value="charts" />';
		$main_text .= '<select name="user" class="FormComboBox" onchange="submit()" style="width: 100px;">';
		$main_text .= '<option value="0">(dowolny)</option>';
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'users_list')
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($key == 'id') $user_id = $value;
							if ($key == 'user_login') $user_login = $value;
						}
						if ($user_id == $selected_user)
							$main_text .= '<option value="'.$user_id.'" selected="selected">'.$user_login.'</option>';
						else
							$main_text .= '<option value="'.$user_id.'">'.$user_login.'</option>';
					}
				}
			}
		}
		$main_text .= '</select>';
		$main_text .= '</form>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Użytkownik:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= '<a href="index.php?route=results&action=options&charts=true" class="PathLink"><img src="img/16x16/options.png" class="TopLinkIcon" alt="options" title="Ustawienia" /></a>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= '<a href="index.php?route=results&action=compare" class="PathLink"><img src="img/16x16/text_list.png" class="TopLinkIcon" alt="compare" title="Porównanie" /></a>';
		$main_text .= '</span>';
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<form action="index.php?route=' . $this->module . '&action=charts&compare=true" method="post">';
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
		
		// wczytuje parametry:
		$this->load_parameters();

		// segreguje dane:
		
		$a_data = array();
		$data_item = array();
		$users_list = array();
				
		// wczytuje wszystkie dane:
		
		foreach ($this->data as $k => $v) 
		{
			foreach ($v as $kk => $vv)
			{
				$packet = FALSE;
				
				if ($kk == 'user_login') $user_login = $vv;
				if ($kk == 'task_type') $task_type = mb_strtoupper($vv);
				if ($kk == 'isi_alg_1') $isi_alg_1 = $vv;
				if ($kk == 'isi_alg_2') $isi_alg_2 = $vv;
				if ($kk == 'learning_trials') { $learning_trials = $vv; $packet = TRUE; }

				if ($packet) // skompletowane dane dla jednego pomiaru
				{
					$data_item = array(
						'isi_alg_1' => $isi_alg_1,
						'isi_alg_2' => $isi_alg_2,
						'learning_trials' => $learning_trials,
					);
					array_push($a_data, array('user_login' => $user_login, 'task_type' => $task_type, 'data' => $data_item));
					
					// ustala użytkowników:
					if (!in_array($user_login, $users_list)) array_push($users_list, $user_login);
				}
			}
		}

		// rozdziela dane na poszczególnych użytkowników:

		foreach ($users_list as $k => $v)
		{
			$canvas_id = $k + 1;
			$user_item = $v;

			$counter = 0;
			$series_points_1 = array();
			$series_points_2 = array();
			$series_points_3 = array();
			$label_points = array();
			
			foreach ($a_data as $k => $v)
			{
				foreach ($v as $kk => $vv)
				{
					if ($kk == 'user_login') $user_login = $vv;
					if ($kk == 'task_type') $task_type = $vv;
					if ($kk == 'data')
					{
						foreach ($vv as $kkk => $vvv)
						{
							if ($kkk == 'isi_alg_1') $isi_alg_1 = $vvv;
							if ($kkk == 'isi_alg_2') $isi_alg_2 = $vvv;
							if ($kkk == 'learning_trials') $learning_trials = $vvv;
						}
						
						if ($user_login == $user_item) // wynik należy do iterowanego użytkownika
						{
							if ($this->p_show_data_1) array_push($series_points_1, $isi_alg_1);
							if ($this->p_show_data_2) array_push($series_points_2, $isi_alg_2);
							if ($this->p_show_data_3) array_push($series_points_3, $learning_trials);
							$counter++;
							array_push($label_points, $counter);
						}					
					}
				}
			}
			
			if ($counter <= MAX_POINTS) // ilość danych nie przekracza ustalonej granicy
			{
				if ($counter) // są wyniki
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $task_type .'</label><br>';
					$main_text .= '<canvas id="canvas-pointers-'. $canvas_id . '" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;"></canvas>';
					$main_text .= '</span>';
					$main_text .= '<script>generateChartSelectedPoints('. $canvas_id . ', '. json_encode($label_points) . ', '. json_encode($series_points_1) . ', '. json_encode($series_points_2) . ', '. json_encode($series_points_3) . ');</script>';
				}
				else // brak wyników
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $task_type .'</label><br>';
					$main_text .= '<img src="gallery/charts/empty.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_standard.'" title="'.$user_item.': Brak danych" />';
					$main_text .= '</span>';
				}
			}
			else // za dużo danych
			{
				$main_text .= '<span style="display: inline-block; padding: 1px;">';
				$main_text .= '<label style="font-weight: bold;">'. $task_type .'</label><br>';
				$main_text .= '<img src="gallery/charts/full.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title_asa.'" title="'.$user_item.': Limit danych" />';
				$main_text .= '</span>';
			}
		}
		
		if (!count($users_list)) // brak danych
		{
			$main_text .= '<span style="padding: 1px;">';
			$main_text .= '<img src="gallery/charts/empty.png" alt="'.$this->p_title_standard.'" title="Brak danych" />';
			$main_text .= '</span>';
		}

		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}	

	/*
	 * Wykresy podsumowania:
	 */
	 
	public function build_summary_charts()
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$this->chart_width = 950;
		$this->chart_height = 400;

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
			$main_text .= '<form action="index.php?route=' . $this->module . '&action=charts" method="post">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['stat_date_from'].'" class="FormInput" style="width: 125px;" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['stat_date_to'].'" class="FormInput" style="width: 125px;" />&nbsp;';
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

		foreach ($this->data as $i => $j)
		{
			$canvas_id = $i + 1;
			
			foreach ($j as $key => $value)
			{
				$counter = 0;
				$series_points = array();
				$label_points = array();

				if ($key == 'date') // według daty
				{
					$this->p_title = 'Liczba wyników dla dni';

					foreach ($value as $k => $v)
					{
						foreach ($v as $kk => $vv)
						{
							if ($kk == 'task_finish_date') $task_finish_date = $vv;
							if ($kk == 'date_counter') $date_counter = $vv;
						}
						array_push($series_points, $date_counter);
						array_push($label_points, substr(str_replace('-', '.', $task_finish_date), 5, 5));
						$counter++;
					}
				}
				if ($key == 'task') // według zadania
				{
					$this->p_title = 'Liczba wyników dla zadań';

					foreach ($value as $k => $v)
					{
						foreach ($v as $kk => $vv)
						{
							if ($kk == 'task_type') $task_type = $vv;
							if ($kk == 'task_counter') $task_counter = $vv;
						}
						array_push($series_points, $task_counter);
						array_push($label_points, str_replace(array('m', '_', 't'), array('', '', '.'), $task_type));
						$counter++;
					}
				}
		
				if ($counter) // są wyniki
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $this->p_title .'</label><br>';
					$main_text .= '<canvas id="canvas-pointers-'. $canvas_id . '" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;"></canvas>';
					$main_text .= '</span>';
					$main_text .= '<script>generateChartSelectedPoints('. $canvas_id .', '. json_encode($label_points) .', '. json_encode(array_reverse($series_points)) .', \'\', \'\');</script>';
				}
				else // brak wyników
				{
					$main_text .= '<span style="display: inline-block; padding: 1px;">';
					$main_text .= '<label style="font-weight: bold;">'. $this->p_title .'</label><br>';
					$main_text .= '<img src="gallery/charts/empty.png" width="'.$this->chart_width.'" height="'.$this->chart_height.'" style="border: 1px solid #999; margin: 5px; padding: 5px;" alt="'.$this->p_title.'" title="'.$this->p_title.': Brak danych" />';
					$main_text .= '</span>';
				}
			}
		}
	
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}
	
	/*
	 * Wykres AJAX-owy:
	 */
	 
	public function build_ajax_chart()
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$this->chart_width = 600;
		$this->chart_height = 300;

		$selected_user = isset($_SESSION['selected_user']) ? $_SESSION['selected_user'] : NULL;

		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';		
		$main_text .= '<span class="UserSelect">';
		$main_text .= '<select id="type" name="type" class="FormComboBox" onchange="filterChartPointers();" style="width: 60px;">';
		$main_text .= '<option value="0">F-G</option>';
		$main_text .= '<option value="1">F-1</option>';
		$main_text .= '<option value="2">F-2</option>';
		$main_text .= '<option value="3">F-3</option>';
		$main_text .= '<option value="4">F-4</option>';
		$main_text .= '<option value="5">F-5</option>';
		$main_text .= '<option value="6">F-6</option>';
		$main_text .= '<option value="10">E-G</option>';
		$main_text .= '<option value="11">E-1</option>';
		$main_text .= '<option value="12">E-2</option>';
		$main_text .= '<option value="13">E-3</option>';
		$main_text .= '<option value="14">E-4</option>';
		$main_text .= '<option value="15">E-5</option>';
		$main_text .= '<option value="16">E-6</option>';
		$main_text .= '</select>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Typ:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelect">';
		$main_text .= '<select id="player" name="player" class="FormComboBox" onchange="filterChartPointers();" style="width: 50px;">';
		$main_text .= '</select>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Gracz:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelect">';
		$main_text .= '<select id="user" name="user" class="FormComboBox" onchange="generateChartPointers();" style="width: 100px;">';
		$main_text .= '<option value="0">(dowolna)</option>';
		if (is_array($this->import))
		{
			foreach ($this->import as $k => $v)
			{
				if ($k == 'users_list')
				{
					foreach ($v as $i => $j)
					{
						foreach ($j as $key => $value)
						{
							if ($key == 'id') $user_id = $value;
							if ($key == 'ip') $user_ip = $value;
							if ($key == 'station') $user_station = $value;
						}
						if ($user_id == $selected_user)
							$main_text .= '<option value="'.$user_id.'" selected="selected">'.$user_station.'</option>';
						else
							$main_text .= '<option value="'.$user_id.'">'.$user_station.'</option>';
					}
				}
			}
		}
		$main_text .= '</select>';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= 'Stacja:';
		$main_text .= '</span>';
		$main_text .= '<span class="UserSelLabel">';
		$main_text .= '<a href="index.php?route=pointers" class="PathLink"><img src="img/16x16/text_list.png" class="TopLinkIcon" alt="pointers" title="Wskaźniki" /></a>';
		$main_text .= '</span>';
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['date_from'].'" class="FormInput" style="width: 125px;" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['date_to'].'" class="FormInput" style="width: 125px;" />&nbsp;';
			$main_text .= '<input type="submit" id="SetDatesButton" name="SetDatesButton" value="OK" style="width: 40px;" onclick="generateChartPointers();" />';
			$main_text .= '</span>';
			$main_text .= '<span class="UserSelLabel">';
			$main_text .= 'Data:';
			$main_text .= '</span>';
		}
		$main_text .= '</th>';
		$main_text .= '</tr>';

		$main_text .= '<tr>';
		$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
		$main_text .= '<div class="chart-legend" id="chart-legend">Wskaźnik globalny</div>';
		$main_text .= '<div id="chart-pointers">';
		$main_text .= '<canvas id="canvas-pointers" width="'.$this->chart_width.'" height="'.$this->chart_height.'"></canvas>';
		$main_text .= '</div>';
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		$main_text .= '<script>generateChartPointers();</script>';
		
		return $main_text;
	}
	
	/*
	 * Wykresy AJAX-owe:
	 */
	 
	public function build_ajax_charts($stats_type)
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$this->chart_width = 600;
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
			switch ($stats_type)
			{
				case STATS_BY_DATES:
					$change_date_range = 'generateChartDates()';
					break;
					
				case STATS_BY_TYPES:
					$change_date_range = 'generateChartTypes()';
					break;
					
				case STATS_BY_DAYS:
					$change_date_range = 'generateChartDays()';
					break;
					
				default:
					break;
			}
			
			$main_text .= '<span class="FormDates">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['stat_date_from'].'" class="FormInput" style="width: 125px;" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['stat_date_to'].'" class="FormInput" style="width: 125px;" />&nbsp;';
			$main_text .= '<input type="submit" id="SetDatesButton" name="SetDatesButton" value="OK" style="width: 40px;" onclick="'.$change_date_range.'" />';
			$main_text .= '</span>';
			$main_text .= '<span class="UserSelLabel">';
			$main_text .= 'Data:';
			$main_text .= '</span>';
		}
		$main_text .= '</th>';
		$main_text .= '</tr>';

		if ($stats_type == STATS_BY_DATES)
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
			$main_text .= '<div class="chart-title">Liczba wyników w poszczególnych dniach</div>';
			$main_text .= '<div id="chart-dates">';
			$main_text .= '<canvas id="canvas-dates" width="'.$this->chart_width.'" height="'.$this->chart_height.'"></canvas>';
			$main_text .= '</div>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}
		
		if ($stats_type == STATS_BY_TYPES)
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
			$main_text .= '<div class="chart-title">Liczba wyników dla poszczególnych typów</div>';
			$main_text .= '<div id="chart-types">';
			$main_text .= '<canvas id="canvas-types" width="'.$this->chart_width.'" height="'.$this->chart_height.'"></canvas>';
			$main_text .= '</div>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}
		
		if ($stats_type == STATS_BY_DAYS)
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
			$main_text .= '<div class="chart-title">Liczba wyników w poszczególnych dniach</div>';
			$main_text .= '<div id="chart-dates">';
			$main_text .= '<canvas id="canvas-days" width="'.$this->chart_width.'" height="'.$this->chart_height.'"></canvas>';
			$main_text .= '</div>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}
		
		$main_text .= '</table>';
		
		switch ($stats_type)
		{
			case STATS_BY_DATES:
				$main_text .= '<script>generateChartDates();</script>';
				break;
				
			case STATS_BY_TYPES:
				$main_text .= '<script>generateChartTypes();</script>';
				break;
				
			case STATS_BY_DAYS:
				$main_text .= '<script>generateChartDays();</script>';
				break;
				
			default:
				break;
		}
		
		return $main_text;
	}

	/*
	 * Wykresy podsumowania:
	 */
	 
	public function build_summary_chart()
	{
		$main_text = NULL;
		
		$cols_count = 1;

		$this->chart_width = 650;
		$this->chart_height = 350;
		
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
						if ($kk == 'counter') $contact_counter = $vv;
					}
					array_push($series_points, $contact_counter);
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
