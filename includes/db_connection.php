<?php

#connect to 'tea' database

#CHANGE THESE SETTINGS TO YOUR SERVER SETTINGS


DEFINE('HOSTNAME','localhost');
DEFINE('USERNAME','root');
DEFINE('PASSWORD','');
DEFINE('DB_NAME','tea');

$dbc = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DB_NAME) or die('could not connect to database '.mysqli_connect_error());

?>