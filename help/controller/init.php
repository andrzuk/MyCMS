<?php

define ('MODULE_NAME', 'init');

$content_title = 'Instalacja serwisu';

include dirname(__FILE__) . '/../../' . HELP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Init_Model($db);

include dirname(__FILE__) . '/../../' . HELP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Init_View($db);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

// pobiera pomoc:
$intro = $model_object->GetIntro();

// wyświetla pomoc:
$site_content = $view_object->ShowIntro($intro);

/*
 * Przechodzi do wygenerowania strony
 */
 
include dirname(__FILE__) . '/../../' . HELP_DIR . 'view/layout.php';

?>
