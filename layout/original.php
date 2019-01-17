<?php

echo '<!DOCTYPE html>';

echo '<html lang="pl">';

echo '<head>';

echo '<meta charset="utf-8">';
echo '<link rel="stylesheet" type="text/css" href="'.$page_data->get_domain().'css/default.css" />';
echo '<link rel="shortcut icon" href="'.$page_data->get_domain().'img/favicon.ico" type="image/x-icon">';
echo '<link rel="icon" href="'.$page_data->get_domain().'img/favicon.ico" type="image/x-icon">';
echo '<script type="text/javascript" src="'.$page_data->get_domain().'js/jquery.1.10.2.js"></script>';
echo '<script type="text/javascript" src="'.$page_data->get_domain().'js/default.js"></script>';
echo '<meta name="keywords" content="'.$page_data->get_keywords().'" />';
echo '<meta name="description" content="'.$page_data->get_description().'" />';
echo '<meta name="author" content="'.$page_data->get_author().'" />';
echo '<meta name="robots" content="index, follow, all" />';
echo '<meta name="googlebot" content="index, follow, all" />';
echo '<meta name="copyright" content="'.$page_data->get_copyright().'" />';
echo '<meta name="classification" content="'.$page_data->get_classification().'" />';
echo '<meta name="publisher" content="'.$page_data->get_publisher().'" />';
echo '<meta name="page-topic" content="'.$page_data->get_topic().'" />';
echo '<title>'.$page_data->get_title().'</title>';
echo '<base href="'.$page_data->get_domain().'" target="_self" />';

echo "<script type=\"text/javascript\">";
echo "  var _gaq = _gaq || [];";
echo "  _gaq.push(['_setAccount', 'UA-16941734-5']);";
echo "  _gaq.push(['_trackPageview']);";
echo "  (function() {";
echo "    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";
echo "    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
echo "    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
echo "  })();";
echo "</script>";

echo '</head>';

echo '<body>';

echo '<div id="container" style="width: '.$page_data->get_site_width().';">';
	echo '<div id="header">';
		echo '<div class="PageHeader">';
			echo '<span class="PageLogo">';
				echo $page_elements->show_header();
			echo '</span>';
			echo '<span class="PageUtilities">';
				echo '<div class="PageLinks">';
					echo $page_data->get_links();
				echo '</div>';
				echo '<div class="PageUser">';
					echo $page_data->get_user();
				echo '</div>';
				echo '<div class="PagePath">';
					echo $page_data->get_path();
				echo '</div>';
			echo '</span>';
		echo '</div>';
		echo '<div class="Clear"></div>';
	echo '</div>';
	echo '<div id="navbar">';
		echo $page_data->get_navbar();
	echo '</div>';
	echo '<div id="center">';
		echo '<div id="menu" style="width: '.$page_data->get_menu_width().';">';
			echo '<div class="PageMenu">';
				echo $page_data->get_menu();
			echo '</div>';
		echo '</div>';
		echo '<div id="content" style="width: '.$page_data->get_content_width().';">';
			echo '<div class="PageContent">';
				echo $page_data->get_content();
			echo '</div>';
		echo '</div>';
		echo '<div class="Clear"></div>';
	echo '</div>';
	echo '<div id="footer">';
		echo '<div class="PageFooter">';
			echo $page_elements->show_footer();
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '</body>';
echo '</html>';

?>