<?php
require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);

    //TEMP
    //TODO

    session_start();
    $_SESSION["admin-username"] = $username;
    $_SESSION["admin-id"] = 1;
    http_response_code(200);
    echo "admin.php";
}

mysqli_close($dbc);
?>