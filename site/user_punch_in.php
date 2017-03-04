<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: signin.html");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $volunteerId = $_SESSION['id'];
    $departmentId = sanitizeInput(getPostParam("department"), $dbc);
    $assignmentId = sanitizeInput(getPostParam("assignment"), $dbc);
    $groupSize = sanitizeInput(getPostParam("group-size"), $dbc);

    $query = "SELECT punch_type
                FROM events
                WHERE volunteer_id = {$volunteerId}
                ORDER BY punch_time DESC;";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($row && $row['punch_type'] == "punch_in") {
        // todo error
        die();
    }

    $query = "SELECT community_service 
                FROM volunteers 
                WHERE volunteer_id = {$volunteerId};";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $cs = $row['community_service'];

    $query = <<<EOT
        INSERT INTO events
            (punch_type,
            punch_time,
            community_service,
            group_size,
            department_id,
            assignment_id,
            volunteer_id)
        VALUES
            ('punch-in',
            NOW(),
            {$cs},
            {$groupSize},
            {$departmentId},
            {$assignmentId},
            {$volunteerId});
EOT;

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    } else {
        http_response_code(200);
        echo "Punch in successful";
    }
}

mysqli_close($dbc);
?>