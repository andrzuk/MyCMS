<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Index_View
{
	public function ShowPage($row)
	{
		$site_content = NULL;
		
		$site_content .= '<div class="PageMainContent">';
		
		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'contents') $site_content .= $value;
			}
		}
		
		$site_content .= '</div>';

		$site_content = empty($row) ? NULL : $site_content;
		
		return $site_content;
	}
	
	public function ShowTitle($row, $import)
	{
		$site_title = NULL;
		$site_modified = NULL;
		$author_login = NULL;
		$site_previews = NULL;
		
		foreach ($import as $i => $j)
		{
			if ($i == 'authors')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $user_id = $v;
						if ($k == 'user_login') $user_login = $v;						
					}
					if ($user_id == $row['author_id']) $author_login = $user_login;
				}
			}
		}

		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'title') $site_title .= $value;
				if ($key == 'modified') $site_modified .= $value;
				if ($key == 'previews') $site_previews .= $value;
			}
			$site_title .= '<span class="PageSignature">';
			$site_title .= '<img src="img/16x16/date.png" class="IconSignature" alt="date" />' . $site_modified;
			$site_title .= '<img src="img/16x16/user.png" class="IconSignature" alt="author" />' . $author_login;
			$site_title .= '<img src="img/16x16/web.png" class="IconSignature" alt="previews" />' . $site_previews;
			$site_title .= '</span>';
		}
		
		return $site_title;
	}
}

?>
