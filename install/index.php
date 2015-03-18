<?php

session_start();

include '../config/config.php';

include '../lib/database.php';

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$db = $connection->open();

include 'controller/init.php';

$connection->close($db);

?>
