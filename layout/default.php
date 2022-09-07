<?php

$render_template = '

<!DOCTYPE html>

<html lang="pl">

<head>

	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="'.$page_data->get_domain().'css/default.css" />
	<link rel="icon" href="'.$page_data->get_domain().'img/favicon.ico" type="image/x-icon">
	<script type="text/javascript" src="'.$page_data->get_domain().'js/chart/Chart.js"></script>
	<script type="text/javascript" src="'.$page_data->get_domain().'js/chart/Ajax.js"></script>
	<script type="text/javascript" src="'.$page_data->get_domain().'js/jquery.1.10.2.js"></script>
	<script type="text/javascript" src="'.$page_data->get_domain().'js/default.js"></script>
	<meta name="keywords" content="'.$page_data->get_keywords().'" />
	<meta name="description" content="'.$page_data->get_description().'" />
	<meta name="author" content="'.$page_data->get_author().'" />
	<meta name="robots" content="index, follow, all" />
	<meta name="googlebot" content="index, follow, all" />
	<meta name="copyright" content="'.$page_data->get_copyright().'" />
	<meta name="classification" content="'.$page_data->get_classification().'" />
	<meta name="publisher" content="'.$page_data->get_publisher().'" />
	<meta name="page-topic" content="'.$page_data->get_topic().'" />
	<title>'.$page_data->get_title().'</title>
	<base href="'.$page_data->get_domain().'" target="_self" />

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-16941734-21"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag() { dataLayer.push(arguments); }
	  gtag("js", new Date());
	  gtag("config", "UA-16941734-21");
	</script>

</head>

<body>

	<div id="container" style="width: '.$page_data->get_site_width().';">
		<div id="header">
			<div class="PageHeader">
				<span class="PageLogo">
					'. $page_elements->show_header() .'
				</span>
				<span class="PageUtilities">
					<div class="PageLinks">
						'. $page_data->get_links() .'
					</div>
					<div class="PageUser">
						'. $page_data->get_user() .'
					</div>
					<div class="PagePath">
						'. $page_data->get_path() .'
					</div>
				</span>
			</div>
		</div>
		<div id="navbar">
			'. $page_data->get_navbar() .'
		</div>
		<div id="center">
			<div id="menu" style="width: '.$page_data->get_menu_width().';">
				<div class="PageMenu">
					'. $page_data->get_menu() .'
				</div>
			</div>
			<div id="content" style="width: '.$page_data->get_content_width().';">
				<div class="PageContent">
					'. $page_data->get_content() .'
				</div>
			</div>
		</div>
		<div id="footer">
			<div class="PageFooter">
				'. $page_elements->show_footer() .'
			</div>
		</div>
	</div>

</body>

</html>

';

echo $render_template;

?>