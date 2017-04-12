<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: ../admin-signin.php");
    exit();
}

require_once('../../global/mysqli_connect.php');
require_once('../../global/utils.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* Input:
        $date:              non-empty, valid date format
        $username:          non-empty
        $groupSize:         non-empty, number
        $communityService
        $punchType:         non-empty, (values: punch-in, punch-out)
        $time:              non-empty, valid time format
        $department:        non-empty, values from db
        $assignment:        non-empty, values from db

        Invalid input behavior: 400 response code + die()
    */

    $date = sanitizeInput(getPostParam("date"), $dbc);
    $username = sanitizeInput(getPostParam("username"), $dbc);
    $groupSize = sanitizeInput(getPostParam("group-size"), $dbc);
    $communityService = sanitizeInput(getPostParam("community-service"), $dbc);
    $punchType = sanitizeInput(getPostParam("punch-type"), $dbc) == "In" ? "punch-in" : "punch-out";
    $time = sanitizeInput(getPostParam("time"), $dbc);
    $departmentId = sanitizeInput(getPostParam("department"), $dbc);
    $assignmentId = sanitizeInput(getPostParam("assignment"), $dbc);
    $volunteerId = 0;

    $validationErrors = [];
    if (!validateDate($date)) {
        $validationErrors[] = "date";
    }
    if (!validateName($username)) {
        $validationErrors[] = "username";
    }
    if (!validateNumber($groupSize)) {
        $validationErrors[] = "groupSize";
    }
    if (!validateTime($time)) {
        $validationErrors[] = "time";
    }
    if (count($validationErrors) > 0) {
        http_response_code(400);
        echo json_encode($validationErrors);
        die();
    }

    // check if department exists
    $query = "SELECT * FROM departments WHERE department_id = '{$departmentId}';";
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$row) {
        http_response_code(400);
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
        http_response_code(400);
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
        http_response_code(400);
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
        INSERT INTO events
        (punch_type, punch_time, community_service, group_size, department_id, assignment_id, volunteer_id)
        VALUES  ('{$punchType}', '{$punchTime}', {$cs}, {$groupSize}, {$departmentId}, {$assignmentId}, {$volunteerId}); 
EOT;
    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    } else {
        http_response_code(200);
        echo "Punch event created";
    }
}

mysqli_close($dbc);
?>