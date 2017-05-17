<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Stats_View
{
	public function ShowPage($row, $import)
	{
		$site_content = NULL;
		
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
		$site_content .= '<th width="80%" class="StatHeader left">Adres odnośnika (referer)</th>';
		$site_content .= '<th width="10%" class="StatHeader right">Odwiedzin</th>';
		$site_content .= '</tr>';

		$iter = 0;

		if (is_array($row))
		{
			foreach ($row as $k => $v)
			{
				$site_content .= '<tr>';
		
				foreach ($v as $key => $value)
				{
					if ($key == 'caption') $caption = $value;
					if ($key == 'licznik') $counter = $value;
				}
				$iter++;
				
				$caption_separated = str_replace(array('=', '&', ':', '?', '+', '-', '/', '%', 'X', 'Y', 'Z', 'Q', 'V'), array(' = ', ' & ', ' : ', ' ? ', ' + ', ' - ', ' / ', ' % ', ' X ', ' Y ', ' Z ', ' Q ', ' V '), $caption);

				$site_content .= '<td class="StatData right offset">'.$iter.'.'.'</td>';
				$site_content .= '<td class="StatData left">'.'<a href="'.$caption.'" target="_blank">'.$caption_separated.'</a>'.'</td>';
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
