<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Map_View
{
	private $site_content;
	private $categories;
	
	public function ShowTree($rows_list, $import)
	{
		$this->categories = $rows_list;
		
		$this->site_content = NULL;
		
		$this->site_content .= '<p style="text-align: left; line-height: 16px;">';
		
		$this->site_content .= '<a href="index.php">'.'Strona główna'.'</a>';
		
		$this->GetChildren(0); // wywołanie rekurencyjnego budowania struktury od root-a (node = 0)
				
		$this->site_content .= '</p>';

		return $this->site_content;
	}
	
	private function GetChildren($node_id)
	{
		$this->site_content .= '<ol>';
		
		foreach ($this->categories as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 'id') $id = $v;
				if ($k == 'parent_id') $parent_id = $v;
				if ($k == 'item_order') $item_order = $v;
				if ($k == 'caption') $caption = $v;
				if ($k == 'link') $link = $v;
				if ($k == 'visible') $visible = $v;
			}
			
			if ($parent_id == $node_id)
			{
				if ($visible)
				{
					$this->site_content .= '<li>';
					$this->site_content .= '<a href="'.$link.'">'.$caption.'</a>';
					$this->site_content .= '</li>';
					
					$this->GetChildren($id); // rekurencyjne zagłębianie w strukturę
				}
			}
		}
		
		$this->site_content .= '</ol>';
	}
}

?>
