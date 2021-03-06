<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: ../admin-signin.php");
    exit();
}

require_once('../../global/mysqli_connect.php');
require_once('../../global/utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = "SELECT assignment_id, assignment_name 
    FROM assignments 
    ORDER BY assignment_name ASC";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $responseRow = [];
        $responseRow['id'] = $row['assignment_id'];
        $responseRow['assignment-name'] = $row['assignment_name'];
        $response[] = $responseRow;
    }

    http_response_code(200);
    echo json_encode($response);
}

?>