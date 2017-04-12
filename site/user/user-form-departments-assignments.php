<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: signin.html");
    exit();
}

require_once('../global/mysqli_connect.php');
require_once('../global/utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $volunteerId = $_SESSION['id'];
    $query = <<<EOT
        SELECT
            department_name,
            assignment_name
        FROM events
        INNER JOIN departments ON events.department_id = departments.department_id
        INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
        WHERE volunteer_id = $volunteerId
        ORDER BY punch_time DESC;
EOT;

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        // todo
        die("error");
    }

    $response['department'] = $row['department_name'];
    $response['assignment'] = $row['assignment_name'];

    http_response_code(200);
    echo json_encode($response);
}

mysqli_close($dbc);
?>