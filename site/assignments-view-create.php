<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignmentName = sanitizeInput(getPostParam("assignment-name"), $dbc);

    if (!validateName($assignmentName)) {
        http_response_code(400);
        echo "assignmentName";
        die();
    }

    $query = "SELECT * FROM assignments WHERE assignment_name='{$assignmentName}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row) {
        http_response_code(300);
        echo "Assignment name in use";
        die();
    }

    $query = "INSERT INTO assignments (assignment_name) VALUES ('{$assignmentName}');";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    http_response_code(200);
    echo "Assignment created";
}

mysqli_close($dbc);
?>