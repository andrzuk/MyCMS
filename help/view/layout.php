<?php

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';

echo '<head>';

echo '<link rel="stylesheet" type="text/css" href="../css/default.css" />';
echo '<link rel="stylesheet" type="text/css" href="../css/install.css" />';
echo '<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">';
echo '<link rel="icon" href="../img/favicon.ico" type="image/x-icon">';
echo '<meta http-equiv="Content-Language" content="pl" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>'.$content_title.'</title>';

echo '</head>';

echo '<body>';

echo '<div id="container" style="width: 1000px;">';

	echo '<div class="HelpHeader">'. '<h1>Instalacja serwisu</h1>' .'</div>';

	echo '<div class="HelpContent">';
	echo $site_content;
	echo '</div>';

	echo '<div class="Clear"></div>';

	echo '<div class="HelpFooter">'. '<h4>Copyright © '.date('Y').' MyMVC Andrzej Żukowski</h4>' .'</div>';

echo '</div>';

echo '</body>';
echo '</html>';

?>
