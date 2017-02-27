<?php
require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $password = sanitizeInput(getPostParam("password"), $dbc);
    $punchType = sanitizeInput(getPostParam("punch-status"), $dbc);

    if ($punchType != 'punch-in' && $punchType != 'punch-out') {
        $punchType = 'punch-in';
    }
    
    $query = "SELECT volunteer_id, password_hash FROM volunteers WHERE username='{$username}'";
    $response = mysqli_query($dbc, $query);

    if (!$response) {
        http_response_code(200);
        echo "Database error";
        die();
    }

    $row = mysqli_fetch_array($response, MYSQLI_ASSOC);
    mysqli_free_result($response);

    if (!$row) {
        http_response_code(200);
        echo "Incorrect username or password";
        die();
    }

    if (!password_verify($password, $row['password_hash'])) {
        http_response_code(200);
        echo "Incorrect username or password";
        die();
    }

    $volunteerID = $row['volunteer_id'];

    $query = "SELECT punch_type, punch_time FROM events WHERE volunteer_id=$volunteerID ORDER BY punch_time DESC";
    $response = mysqli_query($dbc, $query);

    if (!$response) {
        http_response_code(200);
        echo "Database error";
        die();
    }

    $row = mysqli_fetch_array($response, MYSQLI_ASSOC);
    mysqli_free_result($response);

    if (!$row || $row['punch_type'] == 'punch-out') {
        if ($punchType == 'punch-out') {
            http_response_code(200);
            echo "Cannot punch out; not punched in";
            die();
        }
    }

    if ($row['punch_type'] == 'punch-in') {
        if ($punchType == 'punch-in') {
            http_response_code(200);
            echo "Cannot punch in; already punched in";
            die();
        }
    }


    $query = "INSERT INTO events (punch_type, punch_time, volunteer_id) VALUES ('{$punchType}', NOW(), $volunteerID)";
    $response = mysqli_query($dbc, $query);

    if (!$response) {
        http_response_code(200);
        echo "Punch failed, server error";
        die();
    }

    http_response_code(200);
    echo "Punch accepted";
}

mysqli_close($dbc);
?>