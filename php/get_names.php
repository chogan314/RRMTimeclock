<?php
require_once('mysqli_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = "SELECT last_name, first_name FROM volunteers";
    $result = mysqli_query($dbc, $query);

    $response = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $response[] = ucfirst(strtolower($row['last_name'])) . ', ' . ucfirst(strtolower($row['first_name']));
    }

    echo json_encode($response);
}

mysqli_close($dbc);
?>