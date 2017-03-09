<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = "SELECT department_id, department_name 
    FROM departments 
    ORDER BY department_name ASC";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $responseRow = [];
        $responseRow['id'] = $row['department_id'];
        $responseRow['department-name'] = $row['department_name'];
        $response[] = $responseRow;
    }

    http_response_code(200);
    echo json_encode($response);
}

?>