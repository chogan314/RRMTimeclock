<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departmentId = sanitizeInput(getPostParam("id"), $dbc);
    $departmentName = sanitizeInput(getPostParam("department-name"), $dbc);

    if (!validateName($departmentName)) {
        http_response_code(400);
        echo "departmentName";
        die();
    }

    $query = "SELECT department_id, department_name
    FROM departments
    WHERE department_name='{$departmentName}';";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row && $row['department_id'] != $departmentId) {
        http_response_code(300);
        echo "Department name in use";
        die();
    }

    $query = "UPDATE departments
    SET department_name = '{$departmentName}'
    WHERE department_id = '{$departmentId}';";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    http_response_code(200);
    echo "Department updated";
}

mysqli_close($dbc);
?>