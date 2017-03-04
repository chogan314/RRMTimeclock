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

    $query = "SELECT
                punch_type,
                community_service,
                group_size,
                department_id,
                assignment_id
                FROM events
                WHERE volunteer_id = '{$volunteerId}'
                ORDER BY punch_time DESC;";
                
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row || $row['punch_type'] == "punch_out") {
        // todo error
        die("db error");
    }

    $cs = $row['community_service'];
    $groupSize = $row['group_size'];
    $departmentId = $row['department_id'];
    $assignmentId = $row['assignment_id'];

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
            ('punch-out',
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
        echo "Punch out successful";
    }
}

mysqli_close($dbc);
?>