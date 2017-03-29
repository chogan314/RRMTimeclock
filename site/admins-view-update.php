<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

// if (!isset($_SESSION['admin-level']) || $_SESSION['admin-level'] < 5) {
//     header("Location: admin-signin.php");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lastName = sanitizeInput(getPostParam("last-name"), $dbc);
    $firstName = sanitizeInput(getPostParam("first-name"), $dbc);
    $adminLevel = sanitizeInput(getPostParam("admin-level"), $dbc);
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $id = sanitizeInput(getPostParam("id"), $dbc);

    /*
    *   Input validation
    */
    $validationErrors = [];
    if (!validateName($lastName, true)) {
        $validationErrors[] = "lastName";
    }
    if (!validateName($firstName, true)) {
        $validationErrors[] = "firstName";
    }
    if (!validateNumber($adminLevel, true)) {
        $validationErrors[] = "adminLevel";
    }
    if (!validateName($username, true)) {
        $validationErrors[] = "username";
    }
    if (!validatePassword($password, true)) {
        $validationErrors[] = "password";
    }
    if (!validateName($id)) {
        $validationErrors[] = "id";
    }
    if (count($validationErrors) > 0) {
        http_response_code(400);
        echo json_encode($validationErrors);
        die();
    }

    $query = "SELECT admin_id, username FROM admins WHERE admin_id='{$id}';";
    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $match = false;
    if (!$row) {
        http_response_code(200);
        echo "User DNE";
        die();
    } else if ($username != "" && $row['username'] == $username) {
        $match = true;
    }

    if ($username != "") {
        $query = "SELECT username FROM admins WHERE username='{$username}';";
        $result = mysqli_query($dbc, $query);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($row && !$match) {
            http_response_code(200);
            echo "Username in use";
            die();
        }
    }
    
    $lines = [];
    if ($lastName != "") {
        $lines[] = "last_name = '{$lastName}'";
    }
    if ($firstName != "") {
        $lines[] = "first_name = '{$firstName}'";
    }
    if ($adminLevel != "") {
        $lines[] = "admin_level = '{$adminLevel}'";
    }
    if ($username != "") {
        $lines[] = "username = '{$username}'";
    }
    if ($password != "") {
        $lines[] = "password_hash = '{$passwordHash}'";
    }

    $query = "UPDATE admins SET ";
    for ($i = 0; $i < count($lines); $i++) {
        $query .= $lines[$i];
        if ($i != count($lines) - 1) {
            $query .= ", ";
        } else {
            $query .= " ";
        }
    }
    
    $query .= "WHERE admin_id = '{$id}'";
    
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    } else {
        http_response_code(200);
        echo "Account updated";
    }
}
mysqli_close($dbc);
?>