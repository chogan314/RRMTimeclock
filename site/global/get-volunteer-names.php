<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = "SELECT last_name, first_name FROM volunteers;";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $response[] = $row['last_name'] . ', ' . $row['first_name'];
    }

    http_response_code(200);
    echo json_encode($response);
}

mysqli_close($dbc);
?>