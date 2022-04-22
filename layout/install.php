<?php

$render_template = '

<!DOCTYPE html>

<html lang="pl">

<head>

	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="icon" href="img/favicon.ico" type="image/x-icon">
	<meta http-equiv="Content-Language" content="pl" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="index, follow, all" />
	<meta name="googlebot" content="index, follow, all" />
	<title>'.$page_data->get_title().'</title>

</head>

<body>

	<div id="container" style="width: 1000px;">

		<div id="header">
			<div class="PageLinks">
				'. $page_data->get_links() .'
			</div>
			<div class="PageHeader">'. $page_elements->show_header() .'</div>
			<div class="PagePath">
				'. $page_data->get_path() .'
			</div>
		</div>
		
		<div id="center">
			<div id="content" style="width: 100%;">
				<div class="PageContent">
					'. $page_data->get_content() .'
				</div>
			</div>
		</div>

		<div id="footer">
			<div class="PageFooter">'. $page_elements->show_footer() .'</div>
		</div>

	</div>

</body>

</html>

';

echo $render_template;

?>
