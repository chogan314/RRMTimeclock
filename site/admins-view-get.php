<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

// if (!isset($_SESSION['admin-level']) || $_SESSION['admin-level'] < 5) {
//     header("Location: admin-signin.php");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = <<<EOT
        SELECT
            admin_id,
            username,
            first_name,
            last_name,
            admin_level
        FROM admins
        ORDER BY
            last_name ASC,
            first_name ASC,
            username ASC;
EOT;

    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $responseRow = [];
        $responseRow['id'] = $row['admin_id'];
        $responseRow['lastname'] = formatName($row['last_name']);
        $responseRow['firstname'] = formatName($row['first_name']);
        $responseRow['admin-level'] = $row['admin_level'];
        $responseRow['username'] = $row['username'];
        $response[] = $responseRow;
    }

    http_response_code(200);
    echo json_encode($response);
}
?>