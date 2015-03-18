<?php

/*
 * Zbiera dane o wspólnych elementach strony (linki, ścieżka, menu, ...)
 */
 
include 'controller.php';
include 'context.php';

include LIB_DIR . 'message.php';
include LIB_DIR . 'dialog.php';

$site_category = isset($current_category) ? $current_category : NULL;

$status = new Status($db);
$user = $status->get_status();

$context = new Context($db);
$context->init($user, $site_category);
$context->set_options($content_options);
$context->set_current_category($site_category);

$site_links = $context->get_links();
$site_navbar = $context->get_navbar();
$site_menu = $context->get_menu();

$page_data = new PageController($db);
$page_data->init($site_links, $site_path, $site_navbar, $site_menu);
$page_data->set_content($content_title, $site_content);
$page_data->set_user($user);

if (isset($site_message))
{
	$page_message = new Message(NULL);
	$site_msg = $page_message->show_message_box($site_message);
	$page_data->add_content($site_msg);
}

if (isset($site_dialog))
{
	$page_dialog = new Dialog(NULL);
	$site_dlg = $page_dialog->show_dialog_box($site_dialog);
	$page_data->add_content($site_dlg);
}

/*
 * View - generuje stronę
 */
 
include APP_DIR . 'view/template/elements.php';

$page_elements = new Elements($db);
$page_elements->set_header();
$page_elements->set_footer();

if (isset($installation)) // folder "install" istnieje (etap instalacji)
{
	include APP_DIR . 'view/template/install.php';	
}
else // folder "install" nie istnieje (etap eksploatacji)
{
	include APP_DIR . 'view/template/layout.php';
}

?>
