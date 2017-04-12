<?php
require_once('../global/mysqli_connect.php');
require_once('../global/utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);

    $query = "SELECT volunteer_id, password_hash FROM volunteers WHERE username='{$username}';";
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

    $volunteerId = $row['volunteer_id'];
    session_start();
    $_SESSION["username"] = $username;
    $_SESSION["id"] = $volunteerId;
    http_response_code(200);
    echo "user-form.php";
}

mysqli_close($dbc);
?>