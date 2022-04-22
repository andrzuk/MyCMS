<?php

$render_template = '

<!DOCTYPE html>

<html lang="pl">

<head>

	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="stylesheet" type="text/css" href="css/install.css" />
	<link rel="icon" href="img/favicon.ico" type="image/x-icon">
	<meta http-equiv="Content-Language" content="pl" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>'.$content_title.'</title>

</head>

<body>

	<div id="container" style="width: 1000px;">

		<div class="HelpHeader">'. '<h1>'. $content_title .'</h1>' .'</div>

		<div class="HelpContent">
			'. $site_content .'
		</div>

		<div class="HelpFooter">'. '<h4>Copyright © '.date('Y').' MyMVC Andrzej Żukowski</h4>' .'</div>

	</div>

</body>

</html>

';

echo $render_template;

?>
