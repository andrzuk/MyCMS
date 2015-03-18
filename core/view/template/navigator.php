<?php

/*
 * Klasa odpowiedzialna za generowanie nawigatora podziału list
 */

class List_Navigator
{
	private $pointer_band;
	private $current_pointer;
	private $pointer_count;
	private $base_link;
	private $route;
	private $page_rows;
	
	function __construct()
	{
		$this->page_rows = Array(NULL, 5, 10, 15, 20, 50, 100);
	}
	
	public function init($link, $current, $count, $band)
	{
		$this->base_link = $link;
		$this->current_pointer = $current;
		$this->pointer_count = $count;
		$this->pointer_band = $band;

		$address_segment = explode('route=', $this->base_link);
		
		$this->route = sizeof($address_segment) > 1 ? $address_segment[1] : NULL;
	}
	
	public function show()
	{
		$output = NULL;
		
		$output .= '<table class="NaviBar" width="100%" align="center" cellpadding="2" cellspacing="2">';
		$output .= '<tr>';
		
		$output .= '<td class="NaviPointersLeft">';
		$output .= '<form action="'.$this->base_link.'" method="get" class="FormShowRows">';
		$output .= 'wierszy: ';
		$output .= '<input type="hidden" name="route" value="'.$this->route.'" />';
		$output .= '<select name="page_rows" class="FormComboBox" onchange="submit()">';
		foreach ($this->page_rows as $key => $value) 
		{
			$selected = NULL;
			if (isset($_SESSION['page_list_rows']))
			{
				if ($value == $_SESSION['page_list_rows'])
					$selected = 'selected="selected"';
				if ($key == 0) continue;
			}
			$output .= '<option '.$selected.'>';
			$output .= $value;
			$output .= '</option>';
		}
		$output .= '</select>';
		$output .= '</form>';
		$output .= '</td>';
		
		$output .= '<td class="NaviPointers">';
		
		if ($this->current_pointer == 0)
		{
			$output .= '<a class="PagePointerDisabled">&lt;&lt;</a> ';
			$output .= '<a class="PagePointerDisabled">&lt;</a>';
		}
		else
		{
			$output .= '<a href="'.$this->base_link.'&skip=first" class="PagePointer">&lt;&lt;</a> ';
			$output .= '<a href="'.$this->base_link.'&skip=prev" class="PagePointer">&lt;</a>';
		}

		$shown = 1;
		$min_p = intval($this->current_pointer) + 1;
		$max_p = $min_p;
		
		for ($i = 1; $i <= intval($this->pointer_count); $i++)
		{
			$cur_p = $min_p - 1;
			if ($cur_p < $min_p && $cur_p > 0) { $min_p = $cur_p; $shown++; }
			$cur_p = $max_p + 1;
			if ($cur_p > $max_p && $cur_p <= intval($this->pointer_count)) { $max_p = $cur_p; $shown++; }
			if ($shown >= 2 * $this->pointer_band + 1) break;
		}
		for ($i = $min_p; $i <= $max_p; $i++)
		{
			if ($i == $this->current_pointer + 1)
				$output .= ' <b><a class="PagePointerCurrent">'.$i.'</a></b> ';
			else
				$output .= ' <a href="'.$this->base_link.'&page='.$i.'" class="PagePointer">'.$i.'</a> ';
		}

		if (intval($this->current_pointer) == intval($this->pointer_count - 1) || $this->pointer_count == 0)
		{
			$output .= '<a class="PagePointerDisabled">&gt;</a> ';
			$output .= '<a class="PagePointerDisabled">&gt;&gt;</a>';
		}
		else
		{
			$output .= '<a href="'.$this->base_link.'&skip=next" class="PagePointer">&gt;</a> ';
			$output .= '<a href="'.$this->base_link.'&skip=last" class="PagePointer">&gt;&gt;</a>';
		}
		
		$output .= '</td>';
		
		$output .= '<td class="NaviPointersRight">';
		$output .= '<form action="'.$this->base_link.'" method="get" class="FormGoToPage">';
		$output .= 'strona: ';
		$output .= '<input type="hidden" name="route" value="'.$this->route.'" />';
		$output .= '<input type="text" name="page" class="PageInput" /> ';
		$output .= '<input type="submit" name="navi" value="idź" class="PageGo" />';
		$output .= '</form>';
		$output .= '</td>';
		
		$output .= '</tr>';
		$output .= '<tr>';

		$output .= '<td colspan="3" class="NaviPointersCount">';
		$output .= 'Pozycji: <b>' . number_format($_SESSION['result_capacity'], 0, ',', '.') . '</b>';
		$output .= '&nbsp; ▪ &nbsp;';
		$output .= 'Stron: <b>' . number_format($_SESSION['page_counter'], 0, ',', '.') . '</b>';
		$output .= '</td>';
		
		$output .= '</tr>';
		$output .= '</table>';
		
		return $output;
	}
}

?>