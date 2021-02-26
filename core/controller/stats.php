<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'stats');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Stats_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Stats_View($db);

// wyświetla tytuł strony:
$content_title = 'Statystyka wejść na stronę';

// ścieżka strony:
$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=' . MODULE_NAME => $content_title
);

// dane z bazy potrzebne na stronę:

$setting = new Settings($db);

$sponsored_links = $setting->get_config_key('sponsored_links');
$data_import = array('sponsored_links' => $sponsored_links);

$redirect_stats = $setting->get_config_key('redirect_stats');
$redirect_link = array('redirect_stats' => $redirect_stats);

// pobiera statystykę:
$record_object = $model_object->GetStats();

// wyświetla zawartość strony:
$site_content = $view_object->ShowPage($record_object, $data_import, $redirect_link);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
