<?php
// session_start();
// if (!isset($_SESSION['admin-username'])) {
//     header("Location: admin-signin.html");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $endDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $name = sanitizeInput(getGetParam("name"), $dbc);
    $query = "";

    if ($name == "*") {
        $query = <<<EOT
            SELECT
                volunteers.volunteer_id,
                last_name,
                first_name,
                username,
                punch_type,
                punch_time,
                events.community_service,
                group_size,
                assignment
            FROM events INNER JOIN volunteers 
            ON events.volunteer_id = volunteers.volunteer_id
            WHERE punch_time <= '$endDate'
            AND punch_time >= '$startDate'
            ORDER BY username DESC, punch_time DESC
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
                volunteers.volunteer_id,
                last_name,
                first_name,
                username,
                punch_type,
                punch_time,
                events.community_service,
                group_size,
                assignment
            FROM events INNER JOIN volunteers 
            ON events.volunteer_id = volunteers.volunteer_id
            WHERE last_name = '$lastName'
            AND first_name = '$firstName'
            AND punch_time <= '$endDate'
            AND punch_time >= '$startDate'
            ORDER BY username DESC, punch_time DESC
EOT;
    }

    $result = mysqli_query($dbc, $query);

    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];

    $currentUsername = '';
    $hoursAcc = 0;
    $csHoursAcc = 0;
    $volunteersAcc = 0;
    $volunteersSet = [];
    
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    while ($row) {
        if ($row['punch_type'] != 'punch-out') {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            continue;
        }

        $next = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (!$next) {
            break;
        }

        if ($next['username'] != $row['username']) {
            $row = $next;
            continue;
        }

        if ($next['punch_type'] != 'punch-in') {
            // throw error
            break;
        } else {
            $volunteersSet[$next['username']] = 1;
        }

        $groupSize = $next['group_size'];
        $communityService = $next['community_service'];
        $startTime = new DateTime($next['punch_time']);
        $stopTime = new DateTime($row['punch_time']);
        $hours = $stopTime->diff($startTime)->h;

        $hoursAcc += $groupSize * $hours;
        if($communityService) {
            $csHoursAcc += $groupSize * $hours;
        }
        $volunteersAcc += $groupSize;

        $responseRow = [];
        $responseRow['id'] = $row['volunteer_id'];
        $responseRow['date'] = $startTime->format("m-d-Y");
        $responseRow['name'] = $row['last_name'] . ', ' . $row['first_name'];
        $responseRow['username'] = $row['username'];
        $responseRow['groupSize'] = $groupSize;
        $responseRow['communityService'] = $communityService ? 'Yes' : 'No';
        $responseRow['in'] = $startTime->format("h:i A");
        $responseRow['out'] = $stopTime->format("h:i A");
        $responseRow['hours'] = $hours;
        $responseRow['assignment'] = $row['assignment'];
        $response[] = $responseRow;
    }

    http_response_code(200);
    echo json_encode($response);
}

mysqli_close($dbc);
?>