<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'map');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Map_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Map_View($db);

// pobiera wszystkie kategorie:
$records_list = $model_object->GetTree();

// dane z bazy potrzebne na stronę:
$data_import = array();

// wyświetla zawartość strony:
$site_content = $view_object->ShowTree($records_list, $data_import);

// wyświetla tytuł strony:
$content_title = 'Mapa serwisu';

// ścieżka strony:
$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=' . MODULE_NAME => $content_title
);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
