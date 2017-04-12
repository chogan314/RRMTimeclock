<?php
DEFINE ('DB_USER', 'user');
DEFINE ('DB_PASSWORD', 'hunter2');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'timeclock_db');

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    OR die('Could not connect to MySQL: ' . mysqli_connect_error());
?>