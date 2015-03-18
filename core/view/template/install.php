<?php

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';

echo '<head>';

echo '<link rel="stylesheet" type="text/css" href="css/default.css" />';
echo '<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">';
echo '<link rel="icon" href="img/favicon.ico" type="image/x-icon">';
echo '<meta http-equiv="Content-Language" content="pl" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<meta name="robots" content="index, follow, all" />';
echo '<meta name="googlebot" content="index, follow, all" />';
echo '<title>'.$page_data->get_title().'</title>';

echo '</head>';

echo '<body>';

echo '<div id="container" style="width: 1000px;">';

	echo '<div id="header">';

		echo '<div class="PageLinks">';
		echo $page_data->get_links();
		echo '</div>';

		echo '<div class="PageHeader">'. $page_elements->show_header() .'</div>';

		echo '<div class="PagePath">';
		echo $page_data->get_path();
		echo '</div>';

	echo '</div>';
	
	echo '<div id="center">';

		echo '<div id="content" style="width: 100%;">';

			echo '<div class="PageContent">';
			echo $page_data->get_content();
			echo '</div>';

		echo '</div>';
		
		echo '<div class="Clear"></div>';
		
	echo '</div>';

	echo '<div id="footer">';

		echo '<div class="PageFooter">'. $page_elements->show_footer() .'</div>';

	echo '</div>';

echo '</div>';

echo '</body>';
echo '</html>';

?>
