<?php

/*
 * Zbiera dane o wspólnych elementach strony (message, dialog, ...)
 */
 
include '../' . LIB_DIR . 'message.php';
include '../' . LIB_DIR . 'dialog.php';

if (isset($site_message))
{
	$page_message = new Message('../');
	$site_msg = $page_message->show_message_box($site_message);
	$site_content .= $site_msg;
}

if (isset($site_dialog))
{
	$page_dialog = new Dialog('../');
	$site_dlg = $page_dialog->show_dialog_box($site_dialog);
	$site_content .= $site_dlg;
}

/*
 * View - generuje stronę
 */
 
include dirname(__FILE__) . '/../../' . HELP_DIR . 'view/layout.php';

?>
