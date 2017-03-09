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
    // $lastName: empty allowed - do not update on empty
    $lastName = sanitizeInput(getPostParam("last-name"), $dbc);

    // $firstName: empty allowed - do not update on empty
    $firstName = sanitizeInput(getPostParam("first-name"), $dbc);

    $communityService = sanitizeInput(getPostParam("community-service"), $dbc);

    // $username: empty allowed - do not update on empty
    $username = sanitizeInput(getPostParam("username"), $dbc);

    // $password: empty allowed - do not update on empty
    // if not empty, length must be >= 8
    $password = sanitizeInput(getPostParam("password"), $dbc);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $id = sanitizeInput(getPostParam("id"), $dbc);

    $query = "SELECT volunteer_id, username FROM volunteers WHERE volunteer_id='{$id}';";
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
    } else if ($row['username'] == $username) {
        $match = true;
    }

    $query = "SELECT username FROM volunteers WHERE username='{$username}';";
    $result = mysqli_query($dbc, $query);

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($row && !$match) {
        http_response_code(200);
        echo "Username in use";
        die();
    } else {
        $cs = (int)($communityService == 1);
        $query = <<<EOT
            UPDATE volunteers
            SET last_name = '{$lastName}',
                first_name = '{$firstName}',
                community_service = {$cs},
                username = '{$username}',
                password_hash = '{$passwordHash}'
            WHERE volunteer_id = '{$id}';
EOT;
        $result = mysqli_query($dbc, $query);
        if (!$result) {
            die($query."<br/><br/>".mysqli_error($dbc));
        } else {
            http_response_code(200);
            echo "Account updated";
        }
    }
}
mysqli_close($dbc);
?>