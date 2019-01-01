<?php

/*
 * Klasa odpowiedzialna za tworzenie list - Generator List
 */

class ListBuilder
{
	private $title;
	private $image;
	
	private $module;
	
	private $list;
	private $columns;
	private $params;
	private $attribs;
	private $actions;
	private $dates;
	
	function __construct()
	{
	}
	
	public function init($list_title, $list_image)
	{
		$this->image = $list_image;
		$this->title = $list_title;
	}
	
	public function set_module($module)
	{
		$this->module = $module;
	}
	
	public function set_list($list)
	{
		$this->list = $list;
	}
	
	public function set_columns($columns)
	{
		$this->columns = $columns;
	}
	
	public function set_params($params)
	{
		$this->params = $params;
	}
	
	public function set_attribs($attribs)
	{
		$this->attribs = $attribs;
	}
	
	public function set_actions($actions)
	{
		$this->actions = $actions;
	}
	
	public function set_dates($dates)
	{
		$this->dates = $dates;
	}
	
	private function get_split_text($source, $length)
	{
		$result = NULL;
		$idx = 0;
		$broken = FALSE;
		
		$source = str_replace(chr(13) . chr(10), chr(32), $source);
		$words = explode(chr(32), $source);
		
		foreach ($words as $k => $v)
		{
			$result .= $v . chr(32);
			if ($idx++ >= $length) 
			{
				$broken = TRUE;
				break;
			}
		}
		$result = $broken ? $result . '...&nbsp;»' : trim($result);
		
		return $result;
	}
	
	/*
	 * Lista systemowa:
	 */
	 
	public function build_list()
	{
		$main_text = NULL;
		
		$i = 0;
		$idx = 0;
		$line = 0;
		$cols_count = 0;
		$field_names = array();

		// kolumny pól:
		foreach ($this->columns as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 'db_name') $db_name = $v;
				if ($k == 'column_name') $column_name = $v;
				if ($k == 'sorting') $sorting = $v;
			}
			if ($column_name) 
				$field_names[] = array($column_name, $sorting);
		}
		if (sizeof($this->actions))
		{
			// kolumna akcji:
			$field_names[] = array('Akcje', NULL);
		}

		// zlicza kolumny:
		foreach ($field_names as $k => $v)
		{
			if (isset($this->attribs[$i]))
				if ($this->attribs[$i]['visible'])
					$cols_count++;
			$i++;
		}

		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" width="32" height="32" alt="" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '<span class="FormSearch">';
		$main_text .= '<form action="index.php?route=' . $this->module . '&sort=' . $this->params['sort_column'] . '&order=' . intval(1 - $this->params['sort_order']) . '" method="post">';
		$main_text .= '<input type="text" id="ListSearchText" name="ListSearchText" value="" class="FormInput" />&nbsp;';
		$main_text .= '<input type="submit" id="ListSearchButton" name="ListSearchButton" value="Szukaj" style="width: 60px;" />';
		$main_text .= '</form>';
		$main_text .= '</span>';
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<form action="index.php?route=' . $this->module . '&sort=' . $this->params['sort_column'] . '&order=' . intval(1 - $this->params['sort_order']) . '" method="post">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['date_from'].'" class="FormInput" style="width: 120px; padding: 2px;" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['date_to'].'" class="FormInput" style="width: 120px; padding: 2px;" />&nbsp;';
			$main_text .= '<input type="submit" id="SetDatesButton" name="SetDatesButton" value="OK" style="width: 40px;" />';
			$main_text .= '</form>';
			$main_text .= '</span>';
			$main_text .= '<span class="UserSelLabel">';
			$main_text .= 'Data:';
			$main_text .= '</span>';
		}
		$main_text .= '</th>';
		$main_text .= '</tr>';
		
		// jeśli wprowadzono filtr:
		
		if (!empty($_SESSION['list_filter']))
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="FormSearchBar" colspan="'.$cols_count.'">';
			$main_text .= '<form id="form_search_close" action="index.php?route=' . $this->module . '&sort=' . $this->params['sort_column'] . '&order=' . intval(1 - $this->params['sort_order']) . '" method="post">';
			$main_text .= '<span class="FormSearchCaption">Wyszukiwanie:</span>&nbsp;';
			$main_text .= '<span class="FormSearchValue">" <b>' . $_SESSION['list_filter'] . '</b> "</span>';
			$main_text .= '<span class="FormSearchClose">';
			$main_text .= '<input type="hidden" name="ListSearchClose" value="Close" />';
			$main_text .= '<img src="img/16x16/cross_button.png" onclick="document.getElementById(\'form_search_close\').submit();" title="Usuń filtr" />';
			$main_text .= '</span>'; 
			$main_text .= '</form>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}
		
		// nagłówki:
		
		$main_text .= '<tr>';
		
		foreach ($field_names as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 0) $column_name = $value;
				if ($key == 1) $column_sort = $value;
			}
			
			if (++$idx == $this->params['sort_column']) // kolumna aktualnie sortowana
			{
				if ($this->params['sort_order'] == 1) $sort_ico = ' <img src="img/16x16/sort_ascending.png" width="16" height="16" class="SortIcon" />';
				else $sort_ico = ' <img src="img/16x16/sort_descending.png" width="16" height="16" class="SortIcon" />';
			}
			else // pozostałe kolumny
			{
				$sort_ico = '&nbsp;';
			}

			if (isset($this->attribs[$idx - 1]))
			{
				if ($this->attribs[$idx - 1]['visible']) // kolumna widoczna
				{
					if ($column_sort) // kolumna sortowalna
					{
						$main_text .= 	'<td class="TitleCell" width="' . $this->attribs[$idx - 1]['width'] .
											'" style="text-align: ' . $this->attribs[$idx - 1]['align'] . ';"><br>' .
											'<b><a href="index.php?route=' . $this->module .
											'&sort=' . $idx .
											'&order=' . $this->params['sort_order'] .
											'" class="MenuLink">' . $column_name . '</a></b><br>' . $sort_ico . '</td>';
					}
					else // kolumna niesortowalna
					{
						$main_text .= 	'<td class="TitleCell" width="' . $this->attribs[$idx - 1]['width'] .
											'" style="text-align: ' . $this->attribs[$idx - 1]['align'] .
											';"><br><b>' . $column_name . '</b><br>&nbsp;</td>';
					}
				}
			}
		}
		
		$main_text .= '</tr>';
		
		// dane:
		
		if (is_array($this->list))
		{
			foreach ($this->list as $item => $row)
			{
				$class_name = ($line++) % 2 ? 'DataRowDark' : 'DataRowBright';
				
				$data_class_name = 'DataCell';
				
				if (isset($row['user_id'])) $data_class_name = $row['user_id'] > 0 ? 'DataCell' : 'DataLock';
				if (isset($row['result'])) $data_class_name = $row['result'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['visible'])) $data_class_name = $row['visible'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['active'])) $data_class_name = $row['active'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['access'])) $data_class_name = $row['access'] == 1 ? 'DataCell' : 'DataLock';
				
				$main_text .= '<tr class="' . $class_name . '">';
				
				if (is_array($row))
				{
					$idx = 0;
					foreach ($row as $key => $value)
					{
						if ($this->attribs[$idx]['visible']) // kolumna widoczna
						{
							$main_text .= '<td class="'.$data_class_name.'" style="text-align: ' . $this->attribs[$idx]['align'] . ';">';
							
							if (isset($this->attribs[$idx]['image'])) // obrazek
							{
								$main_text .= '<a href="index.php?route=images&action=preview&id=' . $value . '" class="ActionIcon">';
								$main_text .= '<img src="'. GALLERY_DIR . IMG_DIR . $value.'" width="100" height="70" style="border: 1px solid #ccc; padding: 1px;" />';
								$main_text .= '</a>';
							}
							else if (isset($this->attribs[$idx]['icon'])) // ikonka
							{
								$main_text .= '<img src="'. $value.'" style="padding: 2px;" />';
							}
							else // normalne dane
							{
								if (is_array($value)) // dane złożone
								{
									foreach ($value as $k => $v)
									{
										$main_text .= '<div>';
										$main_text .= $this->get_split_text(strip_tags($v), 10);
										$main_text .= '</div>';
									}
								}
								else // pojedyncze dane
								{
									$main_text .= $this->get_split_text(strip_tags($value), 10);
								}
							}
							
							$main_text .= '</td>';
						}
						$idx++;
					}
				}
				
				if (sizeof($this->actions))
				{
					// kolumna akcji:
					$main_text .= '<td class="ActionCell" style="text-align: center;">';
					
					foreach ($this->actions as $k => $v)
					{
						foreach ($v as $key => $value)
						{
							if ($key == 'action') $action = $value;
							if ($key == 'icon') $icon = $value;
							if ($key == 'title') $title = $value;
						}
						$main_text .= '<a href="index.php?route=' . $this->module . '&action=' . $action . '&id=' . $row['id'] . '"><img src="img/16x16/' . $icon . '" class="ActionIcon" width="16" height="16" alt="" title="' . $title . '" /></a>';
					}
									
					$main_text .= '</td>';
				}
				
				$main_text .= '</tr>';
			}
			
			// brak wyników:
			if (!isset($row))
			{
				$main_text .= '<tr>';
				$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
				$main_text .= '<div><img src="img/32x32/warning.png" width="32" height="32" alt="" /></div>';
				$main_text .= '<div>(brak wyników)</div>';
				$main_text .= '</td>';
				$main_text .= '</tr>';
			}
		}
		
		$main_text .= '</table>';
		
		/*
		 * Nawigacja stronami
		 */
		 
		include APP_DIR . 'view/template/navigator.php';
		
		$navi_bar = new List_Navigator();

		$base_link = 'index.php?route=' . $this->module;
						
		$navi_bar->init($base_link, $this->params['show_page'], $this->params['page_counter'], $this->params['page_band']);
		
		$main_text .= $navi_bar->show();

		return $main_text;
	}
	
	/*
	 * Lista wyszukiwania:
	 */
	 
	public function build_found_list()
	{
		$main_text = NULL;
		
		$idx = 0;
		$line = 0;
		$cols_count = 1;
		
		$main_text .= '<table class="Table" width="100%" cellpadding="5" cellspacing="0">';
		
		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'.$cols_count.'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" width="32" height="32" alt="" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '</th>';
		$main_text .= '</tr>';
		
		// dane:
		
		if (is_array($this->list))
		{
			foreach ($this->list as $item => $row)
			{
				$class_name = ($line++) % 2 ? 'DataRowDark' : 'DataRowBright';
				
				$data_class_name = 'DataCell';
				
				if (isset($row['user_id'])) $data_class_name = $row['user_id'] > 0 ? 'DataCell' : 'DataLock';
				if (isset($row['result'])) $data_class_name = $row['result'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['visible'])) $data_class_name = $row['visible'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['active'])) $data_class_name = $row['active'] == 1 ? 'DataCell' : 'DataLock';
				if (isset($row['access'])) $data_class_name = $row['access'] == 1 ? 'DataCell' : 'DataLock';
												
				$main_text .= '<tr class="' . $class_name . '">';
				$main_text .= '<td colspan="'.$cols_count.'" class="'.$data_class_name.'">';
				
				$idx = 0;

				if (is_array($row))
				{
					foreach ($row as $key => $value)
					{
						if ($this->attribs[$idx]['visible'])
						{
							$main_text .= '<div style="' . $this->attribs[$idx]['style'] . '">';
							
							$is_link = $key == 'title' ? TRUE : FALSE;
							
							if ($is_link) $main_text .= '<a href="index.php?route=category&id=' . $row['category_id'] . '">';

							$main_text .= $this->get_split_text(strip_tags($value), 50);

							if ($is_link) $main_text .= '</a>';
							
							$main_text .= '</div>';
						}
						$idx++;
					}
				}
				
				$main_text .= '</td>';
				$main_text .= '</tr>';
			}
			
			// brak wyników:
			if (!isset($row))
			{
				$main_text .= '<tr>';
				$main_text .= '<td class="DataCellMsg" colspan="'.$cols_count.'">';
				$main_text .= '<div><img src="img/32x32/warning.png" width="32" height="32" alt="" /></div>';
				$main_text .= '<div>(brak wyników)</div>';
				$main_text .= '</td>';
				$main_text .= '</tr>';
			}
		}
		
		$main_text .= '</table>';
		
		/*
		 * Nawigacja stronami
		 */
		 
		include APP_DIR . 'view/template/navigator.php';
		
		$navi_bar = new List_Navigator();

		$base_link = 'index.php?route=' . $this->module;
						
		$navi_bar->init($base_link, $this->params['show_page'], $this->params['page_counter'], $this->params['page_band']);
		
		$main_text .= $navi_bar->show();

		return $main_text;
	}
}

?>