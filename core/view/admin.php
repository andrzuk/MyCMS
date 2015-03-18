<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Admin_View
{
	private $width;
	private $image;
	private $title;
	
	public function init($width, $image, $title)
	{
		$this->width = $width;
		$this->image = $image;
		$this->title = $title;
	}
	
	public function ShowPanel($rows)
	{
		$site_content = NULL;
		
		$site_content .= '<table class="Table" width="'.$this->width.'" cellpadding="2" cellspacing="1" align="center">';

		$site_content .= '<tr>';
		$site_content .= '<th class="FormTitleBar">';
		$site_content .= '<span class="FormIcon">';
		$site_content .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$site_content .= '</span>';
		$site_content .= '<span class="FormTitle">';
		$site_content .= $this->title;
		$site_content .= '</span>';
		$site_content .= '</th>';
		$site_content .= '</tr>';
		
		$site_content .= '<tr>';
		$site_content .= '<td>';
		
		foreach ($rows as $i => $j)
		{
			foreach ($j as $row_k => $row_v)
			{
				if ($row_k == 'group') $group = $row_v;
				if ($row_k == 'items') $items = $row_v;
			}
			
			$site_content .= '<div style="text-align: center; font-size: 11px; color: #999; border-bottom: 1px dotted #ccc;">';
			$site_content .= $group;
			$site_content .= '</div>';
			
			$site_content .= '<table width="100%" align="center">';
			$site_content .= '<tr>';
			foreach ($items as $k => $v)
			{
				foreach ($v as $key => $value)
				{
					if ($key == 'address') $address = $value;
					if ($key == 'label') $label = $value;
					if ($key == 'icon') $icon = $value;
					if ($key == 'access') $access = $value;
				}

				$site_content .= '<td class="PanelItem">';

				if ($access) // funkcja dostępna
				{
					$site_content .= '<a href="'.$address.'" class="PanelLink">';
					$site_content .= '<img src="'.$icon.'" class="TopLinkIcon" alt="'.$label.'" />';
					$site_content .= '<div class="ItemEnabled">'.$label.'</div>';
					$site_content .= '</a>';
				}
				else // funkcja niedostępna
				{
					$site_content .= '<img src="'.$icon.'" class="TopLinkIcon" alt="'.$label.'" />';
					$site_content .= '<div class="ItemDisabled">'.$label.'</div>';
				}
				$site_content .= '</td>';
			}
			$site_content .= '</tr>';
			$site_content .= '</table>';
		}
		
		$site_content .= '</td>';
		$site_content .= '</tr>';
		
		$site_content .= '</table>';

		return $site_content;
	}
}

?>