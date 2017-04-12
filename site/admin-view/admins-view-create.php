<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

if (!isset($_SESSION['admin-level']) || $_SESSION['admin-level'] < 5) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lastName = sanitizeInput(getPostParam("last-name"), $dbc);
    $firstName = sanitizeInput(getPostParam("first-name"), $dbc);
    $adminLevel = sanitizeInput(getPostParam("admin-level"), $dbc);
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
    if (!validateNumber($adminLevel)) {
        $validationErrors[] = "adminLevel";
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

    $query = "SELECT username FROM admins WHERE username='{$username}';";
    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row) {
        http_response_code(200);
        echo "Username in use";
    } else {
        $query = <<<EOT
            INSERT INTO admins
            (username, password_hash, first_name, last_name, admin_level)
            VALUES ('{$username}', '{$passwordHash}', '{$firstName}', '{$lastName}', {$adminLevel});
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