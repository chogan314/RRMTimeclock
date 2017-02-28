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

    $id = sanitizeInput(getPostParam("record-id"), $dbc);
    $date = sanitizeInput(getPostParam("date"), $dbc);
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $groupSize = sanitizeInput(getPostParam("group-size"), $dbc);
    $communityService = sanitizeInput(getPostParam("community-service"), $dbc);
    $punchType = sanitizeInput(getPostParam("punch-type"), $dbc) == "In" ? "punch-in" : "punch-out";
    $time = sanitizeInput(getPostParam("time"), $dbc);
    $departmentId = sanitizeInput(getPostParam("department"), $dbc);
    $assignmentId = sanitizeInput(getPostParam("assignment"), $dbc);
    $volunteerId = 0;

    // check if department exists
    $query = "SELECT * FROM departments WHERE department_id = '{$departmentId}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(200);
        echo "Invalid department";
        die();
    }

    // check if assignment exists
    $query = "SELECT * FROM assignments WHERE assignment_id = '{$assignmentId}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(200);
        echo "Invalid assignment";
        die();
    }

    // check if username exists
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

    // get community service value
    $cs = 0;
    if ($communityService == "yes") {
        $cs = 1;
    } else if ($communityService == "no") {
        $cs = 0;
    } else {
        // get community service value from volunteer data
        $query = "SELECT community_service FROM volunteers WHERE volunteer_id = {$volunteerId};";
        $result = mysqli_query($dbc, $query);
        if (!$result) {
            die($query."<br/><br/>".mysqli_error($dbc));
        }
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $cs = $row["community_service"];
    }

    $format = 'Y-m-d H:i';
    $dateTime = DateTime::createFromFormat($format, $date . " " . $time);
    $punchTime = $dateTime->format("Y-m-d H:i:s");

    $query = <<<EOT
        UPDATE events
        SET punch_type = '{$punchType}',
            punch_time = '{$punchTime}',
            community_service = {$cs},
            group_size = {$groupSize},
            department_id = {$departmentId},
            assignment_id = {$assignmentId},
            volunteer_id = {$volunteerId}
        WHERE event_id = {$id};
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