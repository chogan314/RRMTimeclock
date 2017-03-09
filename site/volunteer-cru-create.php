<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Behavior on illegal input - http_respose_code(400) and die();
    // $lastName: empty not allowed
    $lastName = sanitizeInput(getPostParam("last-name"), $dbc);

    // $firstName: empty not allowed
    $firstName = sanitizeInput(getPostParam("first-name"), $dbc);

    $communityService = sanitizeInput(getPostParam("community-service"), $dbc);

    // $username: empty not allowed
    $username = sanitizeInput(getPostParam("username"), $dbc);

    // $password: empty not allowed, length >= 8
    $password = sanitizeInput(getPostParam("password"), $dbc);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 
    $query = "SELECT username FROM volunteers WHERE username='{$username}';";
    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row) {
        http_response_code(200);
        echo "Username in use";
    } else {
        $cs = (int)($communityService == 1);
        $query = <<<EOT
            INSERT INTO volunteers
            (username, password_hash, first_name, last_name, community_service)
            VALUES ('{$username}', '{$passwordHash}', '{$firstName}', '{$lastName}', {$cs});
EOT;
        $result = mysqli_query($dbc, $query);
        if (!$result) {
            die($query."<br/><br/>".mysqli_error($dbc));
        } else {
            http_response_code(200);
            echo "Account created";
        }
    }
}

mysqli_close($dbc);
?>