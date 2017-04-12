<?php
require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);

    $query = "SELECT admin_id, password_hash, admin_level FROM admins WHERE username='{$username}';";
    $response = mysqli_query($dbc, $query);

    if (!$response) {
        http_response_code(500);
        echo "Database error";
        die();
    }

    $row = mysqli_fetch_array($response, MYSQLI_ASSOC);
    mysqli_free_result($response);

    if (!$row) {
        http_response_code(422);
        echo "Invalid username or password";
        die();
    }

    if (!password_verify($password, $row['password_hash'])) {
        http_response_code(422);
        echo "Invalid username or password";
        die();
    }


    session_start();
    $_SESSION["admin-username"] = $username;
    $_SESSION["admin-id"] = $row['admin_id'];
    $_SESSION["admin-level"] = $row['admin_level'];
    http_response_code(200);
    echo "admin.php";
}

mysqli_close($dbc);
?>