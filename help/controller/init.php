<?php

define ('MODULE_NAME', 'init');

include dirname(__FILE__) . '/../../' . HELP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Init_Model();

include dirname(__FILE__) . '/../../' . HELP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Init_View();

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

// pobiera pomoc:
$intro = $model_object->GetIntro($install_exists);

// wyświetla pomoc:
$site_content = $view_object->ShowIntro($intro);

/*
 * Przechodzi do wygenerowania strony
 */

include 'route.php';

?>
