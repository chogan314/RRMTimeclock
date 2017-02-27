<?php
// session_start();
// if (!isset($_SESSION['admin-username'])) {
//     header("Location: admin-signin.html");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* Input:
        $date:              do not update if empty, else valid date format
        $username:          do not update if empty
        $groupSize:         do not update if empty, else number
        $communityService
        $punchType:         do not update if empty, else (values: punch-in, punch-out)
        $time:              do not update if empty, else valid time format
        $department:        do not update if empty, else values from db
        $assignment:        do not update if empty, else values from db

        Invalid input behavior: 400 response code + die()
    */

    $id = sanitizeInput(getPostParam("id"));
    $date = sanitizeInput(getPostParam("date"));
    $username = sanitizeInput(getPostParam("username"));
    $groupSize = sanitizeInput(getPostParam("group-size"));
    $communityService = sanitizeInput(getPostParam("community-service"));
    $punchType = sanitizeInput(getPostParam("punch-type"));
    $time = sanitizeInput(getPostParam("time"));
    $department = sanitizeInput(getPostParam("department"));
    $assignment = sanitizeInput(getPostParam("assignment"));
    $departmentId = 0;
    $assignmentId = 0;
    $volunteerId = 0;

    $query = "SELECT department_id FROM departments WHERE department_name = '{$department}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(200);
        echo "Invalid department";
        die();
    } else {
        $departmentId = $row["department_id"];
    }

    $query = "SELECT assignment_id FROM assignments WHERE assignment_name = '{$assignment}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(200);
        echo "Invalid assignment";
        die();
    } else {
        $assignmentId = $row["assignment_id"];
    }

    $query = "SELECT volunteer_id FROM volunteers WHERE username = '{$username}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(200);
        echo "Invalid username";
        die();
    } else {
        $volunteerId = $row["volunteer_id"];
    }

    $format = 'Y-m-d H:i';
    $dateTime = DateTime::createFromFormat($format, $date . " " . $time);
    $punchTime = $dateTime->format("Y-m-d H:i:s");

    $query = <<<EOT
        UPDATE events
        SET punch_type = '{$punchType}'
            punch_time = '{$punchTime}'
            community_service = '{$communityService}'
            group_size = '{$communityService}'
            department_id = '{$departmentId}'
            assignment_id = '{$assignmentId}'
            volunteer_id = '{$volunteerId}'
        WHERE event_id = '{$id}';
EOT;

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    } else {
        http_response_code(200);
        echo "Punch record updated";
    }
}

mysqli_close($dbc);
?>