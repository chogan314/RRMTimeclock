<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: ../admin-signin.php");
    exit();
}

require_once('../../global/mysqli_connect.php');
require_once('../../global/utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departmentName = sanitizeInput(getPostParam("department-name"), $dbc);

    if (!validateNameWithSpaces($departmentName)) {
        http_response_code(400);
        echo "departmentName";
        die();
    }

    $query = "SELECT * FROM departments WHERE department_name='{$departmentName}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if ($row) {
        http_response_code(300);
        echo "Department name in use";
        die();
    }

    $query = "INSERT INTO departments (department_name) VALUES ('{$departmentName}');";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    http_response_code(200);
    echo "Department created";
}

mysqli_close($dbc);
?>