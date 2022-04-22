<?php

$save_time = $this->mySqlDateTime;

$main_title = $record_item['main_title'];
$main_description = $record_item['main_description'];
$main_keywords = $record_item['main_keywords'];
$base_domain = $record_item['base_domain'];
$short_title = $record_item['short_title'];
$email_sender_address = $record_item['email_sender_address'];
$email_admin_address = $record_item['email_admin_address'];
$email_report_address = $record_item['email_report_address'];
$admin_name = $record_item['admin_name'];
$admin_login = $record_item['admin_login'];
$admin_password = sha1($record_item['admin_password']);

$domain_prefix = 'http://';
$domain_suffix = '/';

if (stristr($base_domain, $domain_prefix) === FALSE)
{
	$base_domain = $domain_prefix . $base_domain;
}
if (substr($base_domain, strlen($base_domain) - 1, 1) != $domain_suffix)
{
	$base_domain .= $domain_suffix;
}

$admin_names = explode(' ', $admin_name, 2);

$first_name = NULL;
$last_name = NULL;

if (is_array($admin_names))
{
	if (isset($admin_names[0])) $first_name = $admin_names[0];
	if (isset($admin_names[1])) $last_name = $admin_names[1];
}

$sql = array(
	array(
		'drop_constraints' => array(
			'ALTER TABLE `documents` DROP FOREIGN KEY `fk_documents_users`;',
			'ALTER TABLE `images` DROP FOREIGN KEY `fk_images_users`;',
			'ALTER TABLE `pages` DROP FOREIGN KEY `fk_pages_users`;',
			'ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_roles_users`;',
			'ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_roles_functions`;',
			'ALTER TABLE `archives` DROP FOREIGN KEY `archives_users`;',
			'ALTER TABLE `archives` DROP FOREIGN KEY `archives_pages`;',
		),
	),
	array(
		'drop_tables' => array(
			'DROP TABLE IF EXISTS `admin_functions`;',
			'DROP TABLE IF EXISTS `archives`;',
			'DROP TABLE IF EXISTS `categories`;',
			'DROP TABLE IF EXISTS `configuration`;',
			'DROP TABLE IF EXISTS `documents`;',
			'DROP TABLE IF EXISTS `hosts`;',
			'DROP TABLE IF EXISTS `images`;',
			'DROP TABLE IF EXISTS `logins`;',
			'DROP TABLE IF EXISTS `pages`;',
			'DROP TABLE IF EXISTS `query_set`;',
			'DROP TABLE IF EXISTS `registers`;',
			'DROP TABLE IF EXISTS `reminds`;',
			'DROP TABLE IF EXISTS `searches`;',
			'DROP TABLE IF EXISTS `users`;',
			'DROP TABLE IF EXISTS `user_messages`;',
			'DROP TABLE IF EXISTS `user_online`;',
			'DROP TABLE IF EXISTS `user_roles`;',
			'DROP TABLE IF EXISTS `visitors`;',
			'DROP TABLE IF EXISTS `visitor_counter`;',
		),
	),
	array(
		'create_tables' => array(
			"
				CREATE TABLE IF NOT EXISTS `admin_functions` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `function` varchar(128) COLLATE utf8_polish_ci NOT NULL,
				  `meaning` varchar(512) COLLATE utf8_polish_ci NOT NULL,
				  `module` varchar(32) COLLATE utf8_polish_ci NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `module` (`module`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;			
			",
			"
				CREATE TABLE IF NOT EXISTS `archives` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `page_id` int(11) unsigned NOT NULL,
				  `main_page` tinyint(1) NOT NULL,
				  `system_page` tinyint(1) NOT NULL,
				  `category_id` int(11) unsigned NOT NULL,
				  `title` varchar(512) CHARACTER SET utf8 NOT NULL,
				  `contents` longtext CHARACTER SET utf8,
				  `author_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  `previews` int(11) unsigned NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `page_id` (`page_id`),
				  KEY `fk_pages_users` (`author_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;			
			",
			"
				CREATE TABLE IF NOT EXISTS `categories` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `type` tinyint(1) NOT NULL,
				  `level` tinyint(1) NOT NULL,
				  `parent_id` int(11) unsigned NOT NULL,
				  `permission` int(11) NOT NULL,
				  `item_order` int(11) NOT NULL,
				  `caption` varchar(128) CHARACTER SET utf8 NOT NULL,
				  `link` varchar(1024) CHARACTER SET utf8 NOT NULL,
				  `icon_id` int(11) unsigned NOT NULL,
				  `page_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `target` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `configuration` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `key_name` varchar(30) NOT NULL,
				  `key_value` varchar(1024) NOT NULL,
				  `meaning` varchar(128) DEFAULT NULL,
				  `field_type` int(11) NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `key` (`key_name`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `documents` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `section_id` int(11) unsigned NOT NULL,
				  `owner_id` int(11) unsigned NOT NULL,
				  `file_format` varchar(32) NOT NULL,
				  `file_name` varchar(512) NOT NULL,
				  `file_size` int(11) NOT NULL,
				  `doc_description` mediumtext,
				  `active` tinyint(1) NOT NULL DEFAULT '1',
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `fk_documents_users` (`owner_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `hosts` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `server_ip` varchar(20) NOT NULL,
				  `server_name` varchar(256) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `server_ip` (`server_ip`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `images` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `section_id` int(11) unsigned NOT NULL,
				  `owner_id` int(11) unsigned NOT NULL,
				  `file_format` varchar(32) NOT NULL,
				  `file_name` varchar(512) NOT NULL,
				  `file_size` int(11) NOT NULL,
				  `picture_width` int(11) NOT NULL,
				  `picture_height` int(11) NOT NULL,
				  `picture_description` mediumtext,
				  `active` tinyint(1) NOT NULL DEFAULT '1',
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `fk_images_users` (`owner_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `logins` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `user_id` int(11) unsigned NOT NULL,
				  `login` varchar(128) NOT NULL,
				  `password` varchar(128) NOT NULL,
				  `login_time` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `pages` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `main_page` tinyint(1) NOT NULL,
				  `system_page` tinyint(1) NOT NULL,
				  `category_id` int(11) unsigned NOT NULL,
				  `title` varchar(512) CHARACTER SET utf8 NOT NULL,
				  `contents` longtext CHARACTER SET utf8,
				  `author_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  `previews` int(11) unsigned NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `category_id` (`category_id`),
				  KEY `fk_pages_users` (`author_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `query_set` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `field` varchar(32) CHARACTER SET utf8 NOT NULL,
				  `operator` varchar(32) CHARACTER SET utf8 NOT NULL,
				  `value` text CHARACTER SET utf8 NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `registers` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `imie` varchar(128) NOT NULL,
				  `nazwisko` varchar(128) NOT NULL,
				  `login` varchar(128) NOT NULL,
				  `email` varchar(128) NOT NULL,
				  `password` varchar(128) NOT NULL,
				  `result` tinyint(1) NOT NULL,
				  `register_time` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `reminds` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `login` varchar(128) NOT NULL,
				  `email` varchar(128) NOT NULL,
				  `result` tinyint(1) NOT NULL,
				  `remind_time` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `searches` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `search_text` varchar(128) NOT NULL,
				  `search_time` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `users` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_login` varchar(32) NOT NULL,
				  `user_password` varchar(48) NOT NULL,
				  `imie` varchar(128) NOT NULL,
				  `nazwisko` varchar(128) NOT NULL,
				  `email` varchar(128) NOT NULL,
				  `status` int(11) NOT NULL DEFAULT '3',
				  `ulica` varchar(64) DEFAULT NULL,
				  `kod` varchar(6) DEFAULT NULL,
				  `miasto` varchar(64) DEFAULT NULL,
				  `pesel` varchar(16) DEFAULT NULL,
				  `telefon` varchar(48) DEFAULT NULL,
				  `data_rejestracji` datetime NOT NULL,
				  `data_logowania` datetime NOT NULL,
				  `data_modyfikacji` datetime NOT NULL,
				  `data_wylogowania` datetime NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `user_login` (`user_login`),
				  KEY `pesel` (`pesel`),
				  KEY `email` (`email`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `user_messages` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `client_ip` varchar(20) NOT NULL,
				  `client_name` varchar(128) NOT NULL,
				  `client_email` varchar(256) NOT NULL,
				  `message_content` longtext NOT NULL,
				  `requested` tinyint(1) NOT NULL,
				  `send_date` datetime NOT NULL,
				  `close_date` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `user_online` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `session` char(100) NOT NULL DEFAULT '',
				  `time` int(11) NOT NULL DEFAULT '0',
				  `user_id` int(11) unsigned NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `user_roles` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL,
				  `function_id` int(11) unsigned NOT NULL,
				  `access` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `user_function` (`user_id`,`function_id`),
				  KEY `fk_roles_functions` (`function_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `visitors` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `visitor_ip` varchar(20) COLLATE utf8_polish_ci NOT NULL,
				  `http_referer` text COLLATE utf8_polish_ci,
				  `request_uri` text COLLATE utf8_polish_ci NOT NULL,
				  `visited` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`),
				  KEY `visitor_ip` (`visitor_ip`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `visitor_counter` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `visitor_ip` varchar(20) NOT NULL,
				  `count` int(11) NOT NULL,
				  `time` varchar(15) NOT NULL,
				  `date` datetime NOT NULL,
				  UNIQUE KEY `id` (`id`),
				  KEY `visitor_ip` (`visitor_ip`),
				  KEY `date` (`date`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
		),
	),
	array(
		'fill_data' => array(
			"
				INSERT INTO `admin_functions` (`id`, `function`, `meaning`, `module`) VALUES
				(1, 'config', 'Konfiguracja', 'config'),
				(2, 'users', 'Użytkownicy', 'users'),
				(3, 'functions', 'Funkcje', 'functions'),
				(4, 'ACL', 'Access Control List', 'roles'),
				(5, 'visitors', 'Odwiedziny', 'visitors'),
				(6, 'gallery', 'Galeria', 'images'),
				(7, 'documents', 'Dokumenty', 'docs'),
				(8, 'categories', 'Kategorie', 'categories'),
				(9, 'pages', 'Strony', 'pages'),
				(10, 'sites', 'Opisy', 'sites'),
				(11, 'messages', 'Wiadomości', 'messages'),
				(12, 'searches', 'Wyszukiwania', 'searches'),
				(13, 'registers', 'Rejestracje', 'registers'),
				(14, 'logins', 'Logowania', 'logins'),
				(15, 'passwords', 'Hasła', 'reminds'),
				(16, 'script', 'Działanie', 'script'),
				(17, 'style', 'Wygląd', 'style'),
				(18, 'layout', 'Układ', 'layout');
			",
			"
				INSERT INTO `configuration` (`id`, `key_name`, `key_value`, `meaning`, `field_type`, `active`, `modified`) VALUES
				(1, 'logo_image', 'gallery/logo/1', 'obrazek logo w nagłówku strony', 1, 1, '$save_time'),
				(2, 'logo_width', '128', 'szerokość obrazka logo w nagłówku strony', 1, 1, '$save_time'),
				(3, 'logo_height', '128', 'wysokość obrazka logo w nagłówku strony', 1, 1, '$save_time'),
				(4, 'page_title', 'Tytuł', 'tytuł w nagłówku strony', 1, 1, '$save_time'),
				(5, 'page_subtitle', 'Podtytuł', 'podtytuł strony internetowej', 1, 1, '$save_time'),
				(6, 'page_footer', '<div id=\"project\">\r\nProjekt nosi nazwę <i>„System zarządzania stroną internetową”</i>. Jest to autorski framework MVC zawierający najważniejsze funkcje systemu CMS.\r\n</div>\r\n<div id=\"slogan\">\r\n<i>„Aktualizacja i rozbudowa strony stała się teraz łatwa i przyjemna.”</i>\r\n</div>\r\n<div>\r\nSerwis używa plików cookies. Więcej informacji na stronie: \"<a href=\"index.php?route=page&id=5\" class=\"FooterLink\">Polityka plików cookies</a>\".\r\n</div>\r\n<div style=\"padding-top: 3px;\">\r\n© {_year_} MyMVC <a href=\"https://plus.google.com/113303165754486219878?rel=author\" class=\"FooterLink\" target=\"_blank\">Andrzej Żukowski</a>. Wszystkie prawa zastrzeżone.', 'treść w stopce strony', 2, 1, '$save_time'),
				(7, 'main_title', '$main_title', 'tytuł strony internetowej', 2, 1, '$save_time'),
				(8, 'main_description', '$main_description', 'meta tag descriptions nagłówka strony', 2, 1, '$save_time'),
				(9, 'main_keywords', '$main_keywords', 'meta dane keywords strony internetowej', 2, 1, '$save_time'),
				(10, 'main_author', 'application logic & design: Andrzej Żukowski © 2014', 'autor serwisu - logiki biznesowej i designu', 2, 1, '$save_time'),
				(11, 'main_copyright', '$short_title', 'prawa autorskie serwisu', 1, 1, '$save_time'),
				(12, 'main_classification', 'CMS & MVC Project', 'klasyfikacja serwisu', 2, 1, '$save_time'),
				(13, 'main_publisher', '$base_domain', 'wydawca serwisu', 1, 1, '$save_time'),
				(14, 'main_page_topic', '$main_title', 'topic serwisu', 2, 1, '$save_time'),
				(15, 'base_domain', '$base_domain', 'domena (adres) serwisu', 1, 1, '$save_time'),
				(16, 'main_site_width', '1024px', 'szerokość strony w procentach lub pikselach', 1, 1, '$save_time'),
				(17, 'menu_panel_width', '18%', 'szerokość panela menu w procentach lub pikselach', 1, 1, '$save_time'),
				(18, 'content_panel_width', '80%', 'szerokość panela głównej treści w procentach lub pikselach', 1, 1, '$save_time'),
				(19, 'navbar_panel_visible', 'true', 'górny panel nawigacji widoczny', 3, 1, '$save_time'),
				(20, 'options_panel_visible', 'true', 'panel menu kontekstowego widoczny', 3, 1, '$save_time'),
				(21, 'options_panel_title', 'Opcje', 'tytuł panelu menu kontekstowego (panelu opcji)', 1, 1, '$save_time'),
				(22, 'menu_panel_visible', 'true', 'panel menu widoczny', 3, 1, '$save_time'),
				(23, 'menu_panel_title', 'Menu', 'tytuł panelu menu (kategorii)', 1, 1, '$save_time'),
				(24, 'search_panel_visible', 'true', 'panel szybkiego wyszukiwania widoczny', 3, 1, '$save_time'),
				(25, 'search_panel_title', 'Szukaj', 'tytuł panelu szybkiego wyszukiwania', 1, 1, '$save_time'),
				(26, 'stats_panel_visible', 'true', 'panel statystyk widoczny', 3, 1, '$save_time'),
				(27, 'stats_panel_title', 'Info', 'tytuł sekcji statystyk (tuż pod menu)', 1, 1, '$save_time'),
				(28, 'facebook_panel_visible', 'true', 'panel ikonek do facebooka widoczny', 3, 1, '$save_time'),
				(29, 'facebook_panel_title', 'Znajdź nas', 'tytuł panelu z ikonkami do facebooka', 1, 1, '$save_time'),
				(30, 'display_list_rows', '20', 'liczba wierszy listy na jednej stronie', 1, 1, '$save_time'),
				(31, 'description_length', '175', 'maksymalna długość opisu pozycji na liście znalezionych', 1, 1, '$save_time'),
				(32, 'page_pointer_band', '4', 'liczebność (połowa) paska ze wskaźnikami stron w pasku nawigacji', 1, 1, '$save_time'),
				(33, 'using_office_editor', 'false', 'użycie edytora tekstów typu WYSIWYG (układ Office-a)', 3, 1, '$save_time'),
				(34, 'starting_office_editor', 'false', 'automatyczne uruchomienie edytora tekstów typu WYSIWYG (układ Office-a)', 3, 1, '$save_time'),
				(35, 'office_editor_location', 'lib/editor', 'położenie edytora Office-a wykorzystywanego do edycji artykułów', 2, 1, '$save_time'),
				(36, 'chart_library_path', '../pChart', 'ścieżka do biblioteki renderowania wykresów', 2, 1, '$save_time'),
				(37, 'chart_images_path', '../pChart/tmp/', 'ścieżka do wyrenderowanych obrazów wykresów', 1, 1, '$save_time'),
				(38, 'chart_width', '740', 'szerokość wykresu statystyk serwisu', 1, 1, '$save_time'),
				(39, 'chart_height', '350', 'wysokość wykresu statystyk serwisu', 1, 1, '$save_time'),
				(40, 'send_restricted_report', 'false', 'wysyłanie e-mailem raportów do admina o użyciu zabronionego słowa w formularzu', 3, 1, '$save_time'),
				(41, 'send_new_comment_report', 'false', 'wysyłanie e-mailem raportów do admina o pojawieniu się nowego komentarza', 3, 1, '$save_time'),
				(42, 'send_new_message_report', 'true', 'wysyłanie e-mailem raportów do admina o pojawieniu się nowej wiadomości', 3, 1, '$save_time'),
				(43, 'sponsored_links', 'https://java-blog-cms.herokuapp.com; https://mean-stack-web.herokuapp.com; https://java-cms.herokuapp.com; http://metodycznie.com.pl', 'linki do innych stron wstrzyknięte do raportu statystyk', 2, 1, '$save_time'),
				(44, 'excluded_domains', 'mycms.pl, fast-cms.pl, metodycznie.pl, swoja-strona.eu, active-cms.eu, mvc.net.pl, cms-jfrmwrk.rhcloud.com', 'domeny wyłączone z raportu statystyk', 2, 1, '$save_time'),
				(45, 'black_list_visitors', '\'255.255.255.255\', \'1.1.1.1\'', 'lista zabronionych adresów IP', 2, 1, '$save_time'),
				(46, 'black_list_messages_limit', '5', 'limit wiadomości wysyłanych seryjnie powodujący dopisanie do czarnej listy', 1, 1, '$save_time'),
				(47, 'links_length_min', '50', 'minimalna długość linku wyświetlanego w raporcie statystyk', 1, 1, '$save_time'),
				(48, 'links_length_max', '600', 'maksymalna długość linku wyświetlanego w raporcie statystyk', 1, 1, '$save_time'),
				(49, 'email_sender_name', 'Serwis $short_title - Mail Manager', 'nazwa konta e-mailowego serwisu', 1, 1, '$save_time'),
				(50, 'email_sender_address', '$email_sender_address', 'adres konta e-mailowego serwisu', 1, 1, '$save_time'),
				(51, 'email_admin_address', '$email_admin_address', 'adres e-mail administratora serwisu', 1, 1, '$save_time'),
				(52, 'email_report_address', '$email_report_address', 'adres e-mail odbiorcy raportów', 1, 1, '$save_time'),
				(53, 'email_report_subject', 'Raport serwisu $short_title', 'temat maila raportującego zdarzenie', 1, 1, '$save_time'),
				(54, 'email_report_body_1', 'Raport o zdarzeniu w serwisie $short_title', 'treść maila rapotującego - część przed zmiennymi', 2, 1, '$save_time'),
				(55, 'email_report_body_2', '(brak)', 'treść maila rapotującego - część za zmiennymi', 2, 1, '$save_time'),
				(56, 'email_createcnt_subject', 'Serwis $short_title - rejestracja konta', 'temat generowanego maila z potwierdzeniem rejestracji', 1, 1, '$save_time'),
				(57, 'email_createcnt_body_1', 'Dziękujemy za rejestrację w serwisie $short_title.\r\nParametry Twojego konta są następujące:', 'treść generowanego maila z potwierdzeniem rejestracji - przed parametrami', 2, 1, '$save_time'),
				(58, 'email_createcnt_body_2', 'Przypominamy, że hasło możesz zmienić po zalogowaniu, natomiast login musi pozostać nie zmieniony, gdyż jest identyfikatorem Twojego konta.', 'treść generowanego maila z potwierdzeniem rejestracji - po parametrach', 2, 1, '$save_time'),
				(59, 'email_editcnt_subject', 'Serwis $short_title - edycja konta użytkownika', 'temat generowanego maila edycji konta użytkownika', 2, 1, '$save_time'),
				(60, 'email_editcnt_body_1', 'Informujemy, że dokonałeś zmian w ustawieniach swojego konta.\r\nParametry Twojego konta są następujące:', 'wstęp e-maila do użytkownika o zmianie parametrów konta', 2, 1, '$save_time'),
				(61, 'email_editcnt_body_2', 'Przypominamy, że parametrów ''login'' i ''PESEL'' nie można zmienić, gdyż są one identyfikatorami konta w serwisie.', 'zakończenie e-maila do użytkownika o zmianie parametrów konta', 2, 1, '$save_time'),
				(62, 'email_remindpwd_subject', 'Serwis $short_title - nowe hasło do konta', 'temat generowanego maila z nowym hasłem', 1, 1, '$save_time'),
				(63, 'email_remindpwd_body_1', 'Na Twoją prośbę przesyłamy Ci hasło do konta w serwisie $short_title.\r\nOto Twój login, PESEL oraz nowe hasło:', 'treść generowanego maila z nowym hasłem - przed hasłem', 2, 1, '$save_time'),
				(64, 'email_remindpwd_body_2', 'Zaloguj się, a następnie zmień hasło na swoje własne.', 'treść generowanego maila z nowym hasłem - za hasłem', 2, 1, '$save_time');
			",
			"
				INSERT INTO `pages` (`id`, `main_page`, `system_page`, `category_id`, `title`, `contents`, `author_id`, `visible`, `modified`, `previews`) VALUES
				(1, 1, 1, 0, 'Strona główna', '<h1>Serwis $short_title</h1><h2>$main_title</h2><h3>Strona główna</h3><p>$main_description</p>', 1, 1, '$save_time', 0),
				(2, 2, 1, 0, 'Kontakt', '<style>\r\np.contact { margin: 0; padding: 0; }\r\ntd.form { vertical-align: top; margin: 0; padding: 0 0 10px 0; }\r\ntd.map { padding-bottom: 20px; text-align: center; }\r\nh2.info, h3.info { margin: 0; padding: 0px 0px 10px 50px; }\r\n</style>\r\n\r\n<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n<tr>\r\n<td class=\"map\" colspan=\"2\">\r\n<p.contact style=\"text-align: center;\">\r\n<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4867.701649478604!2d16.91226795315743!3d52.40937977092189!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0000000000000000%3A0x6201d17fe9c37f41!2sDZ+Bank+Polska+S.A.+Centrum+Bankowo%C5%9Bci+Korporacyjnej!5e0!3m2!1spl!2sus!4v1417506974043\" width=\"100%\" height=\"300\" frameborder=\"0\" style=\"border: #aaa 1px solid;\"></iframe>\r\n\r\n</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td class=\"form\">\r\n<h2 class=\"info\">e-mail: <a href=\"mailto:andrzuk@tlen.pl\">andrzuk@tlen.pl</a></h2>\r\n<h2 class=\"info\">#GG: <a href=\"gg:5684331\">5684331</a></h2>\r\n<h2 class=\"info\">twitter: <a href=\"https://twitter.com/andy_zukowski\" target=\"_blank\">@andy_zukowski</a></h2>\r\n<h2 class=\"info\">facebook: <a href=\"https://www.facebook.com/zukowski.andrzej\" target=\"_blank\">/zukowski.andrzej</a></h2>\r\n<h2 class=\"info\">LinkedIn: <a href=\"https://pl.linkedin.com/in/andrzejzukowski\" target=\"_blank\">/andrzejzukowski</a></h2>\r\n</td>\r\n<td>\r\n{_contact_form_}\r\n</td>\r\n</tr>\r\n</table>', 1, 1, '$save_time', 0),
				(3, 0, 1, 0, 'Regulamin serwisu', 'Regulamin serwisu.', 1, 1, '$save_time', 0),
				(4, 0, 1, 0, 'Pomoc techniczna', 'Pomoc techniczna.', 1, 1, '$save_time', 0),
				(5, 0, 1, 0, 'Polityka plików cookies', 'Polityka plików cookies.', 1, 1, '$save_time', 0);
			",
			"
				INSERT INTO `users` (`id`, `user_login`, `user_password`, `imie`, `nazwisko`, `email`, `status`, `ulica`, `kod`, `miasto`, `pesel`, `telefon`, `data_rejestracji`, `data_logowania`, `data_modyfikacji`, `data_wylogowania`, `active`) VALUES
				(1, '$admin_login', '$admin_password', '$first_name', '$last_name', '$email_admin_address', 1, '', '', '', '', '', '$save_time', '$save_time', '$save_time', '$save_time', 1);
			",
			"
				INSERT INTO `user_roles` (`id`, `user_id`, `function_id`, `access`) VALUES
				(1, 1, 1, 1),
				(2, 1, 2, 1),
				(3, 1, 3, 1),
				(4, 1, 4, 1),
				(5, 1, 5, 1),
				(6, 1, 6, 1),
				(7, 1, 7, 1),
				(8, 1, 8, 1),
				(9, 1, 9, 1),
				(10, 1, 10, 1),
				(11, 1, 11, 1),
				(12, 1, 12, 1),
				(13, 1, 13, 1),
				(14, 1, 14, 1),
				(15, 1, 15, 1),
				(16, 1, 16, 1),
				(17, 1, 17, 1),
				(18, 1, 18, 1);
			",
			"
				INSERT INTO `query_set` (`id`, `field`, `operator`, `value`) VALUES
				(1, 'period_from', '=', '2019-01-01'),
				(2, 'period_to', '=', '2019-12-31'),
				(3, 'exceptions', '=', '\'localhost\''),
				(4, 'modified', '=', '$save_time');
			",
		),
	),
	array(
		'create_constraints' => array(
			'
				ALTER TABLE `documents`
				  ADD CONSTRAINT `fk_documents_users` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `images`
				  ADD CONSTRAINT `fk_images_users` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `pages`
				  ADD CONSTRAINT `fk_pages_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `user_roles`
				  ADD CONSTRAINT `fk_roles_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
				  ADD CONSTRAINT `fk_roles_functions` FOREIGN KEY (`function_id`) REFERENCES `admin_functions` (`id`);
			',
			'
				ALTER TABLE `archives`
				  ADD CONSTRAINT `archives_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
				  ADD CONSTRAINT `archives_pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`);
			',
		),
	),
);

?>
