<?php

/*
 * Application directories
 */
 
define ('APP_DIR', 'core/');         // model-view-controller directory
define ('LIB_DIR', 'lib/');          // application library directory
define ('GALLERY_DIR', 'gallery/');  // gallery directory
define ('IMG_DIR', 'images/');       // images directory
define ('DOC_DIR', 'docs/');         // documents directory
define ('SND_DIR', 'sounds/');       // sounds directory
define ('HELP_DIR', 'help/');        // help directory
define ('INSTALL_DIR', 'install/');  // install directory
define ('LAYOUT_DIR', 'layout/');    // layout directory

/*
 * Database connection
 */
 
define ('DB_HOST', '');  // db hostname
define ('DB_NAME', '');  // db name
define ('DB_USER', '');  // db username
define ('DB_PASS', '');  // db password  

/*
 * User groups to resource access levels
 */
 
define ('GUEST', 0); 
define ('ADMIN', 1); 
define ('OPERATOR', 2); 
define ('USER', 3); 
define ('FREE', 4); 

/*
 * Others
 */
 
define ('PASS_MASK', '********'); 
define ('PAGE_IMPORT_TEMPLATE', '{_import_page_}&id='); 
define ('CONTACT_FORM', '{_contact_form_}');

/* Before database starts: */
define ('PAGE_LOGO', GALLERY_DIR.'logo/logo.png');  // main logo
define ('PAGE_TITLE', 'Moja');                      // main title
define ('PAGE_SUBTITLE', 'strona domowa');          // main subtitle

?>
