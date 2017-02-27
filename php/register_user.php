<?php
require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = sanitizeInput($_POST["first-name"], $dbc);
    $lastName = sanitizeInput($_POST["last-name"], $dbc);
    $username = sanitizeInput($_POST["create-username"], $dbc);
    $password = sanitizeInput($_POST["create-password"], $dbc);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT username FROM volunteers WHERE username='{$username}'";
    $response = mysqli_query($dbc, $query);

    if (!$response) {
        http_response_code(200);
        echo "Database error";
    } else {
        $row = mysqli_fetch_array($response, MYSQLI_NUM);
        mysqli_free_result($response);
        if ($row) {
            http_response_code(200);
            echo "Username in use";
        } else {
            $query = "INSERT INTO volunteers (username, password_hash, first_name, last_name) VALUES ('{$username}', '{$passwordHash}', '{$firstName}', '{$lastName}')";
            $response = mysqli_query($query);
            if (!$response) {
                http_response_code(200);
                echo "Account creation failed";
            } else {
                http_response_code(200);
                echo "Account created";
            }
        }
    }
}

mysqli_close($dbc);
?>