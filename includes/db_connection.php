<?php

#connect to 'tea' database

DEFINE('HOSTNAME','localhost');
DEFINE('USERNAME','root');
DEFINE('PASSWORD','');
DEFINE('DB_NAME','tea');

$dbc = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DB_NAME) or die('count not connect to database '.mysqli_connect_error());

?>