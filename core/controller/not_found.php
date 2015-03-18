<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

$content_title = 'Strona nie znaleziona';

$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=not_found' => $content_title
);

/*
 * Model - pobiera treść podstrony z bazy
 */

$site_content = NULL;
$content_options = array();

$site_dialog = array(
	'ERROR',
	'Błąd podstrony',
	'Strona nie została znaleziona.'.
	'<br />Sprawdź, czy podany adres jest prawidłowy.'.
	'<br />Poprawny adres powinien mieć postać:'.
	'<br />index.php?route={moduł}[&action={działanie}[&id={numer}]]',
	array(
		array(
			'index.php', 'Zamknij'
		)
	)
);

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
 
include 'main/route.php';

?>
