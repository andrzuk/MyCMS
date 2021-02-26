<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Stats_View
{
	public function ShowPage($row, $import, $redirect)
	{
		$site_content = NULL;
		$redirect_stats = NULL;
		
		if (is_array($redirect))
		{
			foreach ($redirect as $i => $j)
			{
				if ($i == 'redirect_stats') $redirect_stats = $j;
			}
		}
		
		if ($redirect_stats)
		{
			/*
			$site_content .= '
				<script type="text/javascript">
					window.location.href = "' . $redirect_stats . '"
				</script>
			';
			return $site_content; // REDIRECT web page
			*/
			header('Location: ' . $redirect_stats); 
			exit;
		}
		
		$site_content .= '
			<style>
				th { padding-top: 10px; padding-bottom: 10px; border-bottom: 1px solid #666; }
				td.left, td.right { padding-top: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc; }
				th.left, td.left { padding-left: 20px; }
				th.right, td.right { text-align: right; }
				th.offset, td.offset { padding-right: 30px; }
			</style>
		';
		
		$site_content .= '<p>';
		
		$site_content .= '<table width="90%" align="center">';

		$site_content .= '<tr>';
		$site_content .= '<th width="10%" class="StatHeader right offset">Lp.</th>';
		$site_content .= '<th width="75%" class="StatHeader left">Adres odnośnika (referer)</th>';
		$site_content .= '<th width="6%" class="StatHeader center">Lk</th>';
		$site_content .= '<th width="9%" class="StatHeader right">Odwiedzin</th>';
		$site_content .= '</tr>';

		$iter = 0;
		$sponsored_links = array();

		if (is_array($row))
		{
			if (is_array($import))
			{
				foreach ($import as $i => $j)
				{
					if ($i == 'sponsored_links') $links = $j;
				}
				$sponsored_links = explode('; ', $links);
			}
			foreach ($row as $k => $v)
			{
				$sponsored_link = $iter < count($sponsored_links) ? '<a href="'.$sponsored_links[$iter].'" target="_blank"><img src="img/16x16/star.png" alt="'.$sponsored_links[$iter].'" title="Sponsored link"></a>' : NULL;
				
				$sponsored_link_stars = NULL;
				for ($s = 3 - $iter; $s > 0; $s--)
				{
					$sponsored_link_stars .= $sponsored_link;
				}
				
				foreach ($v as $key => $value)
				{
					if ($key == 'caption') $caption = $value;
					if ($key == 'licznik') $counter = $value;
				}
				
				$iter++;
				
				$caption_separated = str_replace(array('=', '&', ':', '?', '+', '-', '/', '%', 'X', 'Y', 'Z', 'Q', 'V'), array(' = ', ' & ', ' : ', ' ? ', ' + ', ' - ', ' / ', ' % ', ' X ', ' Y ', ' Z ', ' Q ', ' V '), $caption);

				$site_content .= '<tr>';		
				$site_content .= '<td class="StatData right offset">'.$iter.'.'.'</td>';
				$site_content .= '<td class="StatData left">'.'<a href="'.$sponsored_links[$iter % count($sponsored_links)].'" target="_blank">'.$caption_separated.'</a>'.'</td>';
				$site_content .= '<td class="StatData center">'.$sponsored_link_stars.'</td>';
				$site_content .= '<td class="StatData right">'.$counter.'</td>';
				$site_content .= '</tr>';
			}
		}
		
		$site_content .= '</table>';

		$site_content .= '</p>';

		return $site_content;
	}
}

?>
