<?php
// session_start();
// if (!isset($_SESSION['admin-username'])) {
//     header("Location: admin-signin.html");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $name = sanitizeInput(getGetParam("name"), $dbc);
    
    $query = "";

    if ($name == "*") {
        $query = <<<EOT
            SELECT
                volunteer_id,
                last_name,
                first_name,
                community_service,
                username
            FROM volunteers
            ORDER BY last_name DESC, first_name DESC, username DESC;
EOT;
    } else {
        if (count(explode(",", $name)) != 2) {
            die();
            // todo: handle error
        }

        $splitName = splitName($name);
        $lastName = $splitName[0];
        $firstName = $splitName[1];

        $query = <<<EOT
            SELECT
                volunteer_id,
                last_name,
                first_name,
                community_service,
                username
            FROM volunteers
            WHERE last_name = '$lastName'
            AND first_name = '$firstName'
            ORDER BY username DESC;
EOT;
    }

    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $responseRow = [];
        $responseRow['id'] = $row['volunteer_id'];
        $responseRow['lastname'] = $row['last_name'];
        $responseRow['firstname'] = $row['first_name'];
        $responseRow['community-service'] = $row['community_service'] ? 'Yes' : 'No';
        $responseRow['username'] = $row['username'];
        $response[] = $responseRow;
    }

    http_response_code(200);
    echo json_encode($response);
}
?>