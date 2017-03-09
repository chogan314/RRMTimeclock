<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignmentId = sanitizeInput(getPostParam("id"), $dbc);
    $assignmentName = sanitizeInput(getPostParam("assignment-name"), $dbc);

    $query = "SELECT assignment_id, assignment_name
    FROM assignments
    WHERE assignment_name='{$assignmentName}';";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row && $row['assignment_id'] != $assignmentId) {
        http_response_code(300);
        echo "Assignment name in use";
        die();
    }

    $query = "UPDATE assignments
    SET assignment_name = '{$assignmentName}'
    WHERE assignment_id = '{$assignmentId}';";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    http_response_code(200);
    echo "Assignment updated";
}

mysqli_close($dbc);
?>