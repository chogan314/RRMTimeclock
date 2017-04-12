<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lastName = sanitizeInput(getPostParam("last-name"), $dbc);
    $firstName = sanitizeInput(getPostParam("first-name"), $dbc);
    $communityService = sanitizeInput(getPostParam("community-service"), $dbc);
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /*
    *   Input validation
    */
    $validationErrors = [];
    if (!validateName($lastName)) {
        $validationErrors[] = "lastName";
    }
    if (!validateName($firstName)) {
        $validationErrors[] = "firstName";
    }
    if (!validateName($username)) {
        $validationErrors[] = "username";
    }
    if (!validatePassword($password)) {
        $validationErrors[] = "password";
    }
    if (count($validationErrors) > 0) {
        http_response_code(400);
        echo json_encode($validationErrors);
        die();
    }

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