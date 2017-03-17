<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $endDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $name = sanitizeInput(getGetParam("name"), $dbc);
    $query = "";
    $usernameSearch = false;
    $username = "";

    $validationErrors = [];
    if (!validateDate($startDate)) {
        $validationErrors[] = "startDate";
    }
    if (!validateDate($endDate)) {
        $validationErrors[] = "endDate";
    }
    if ($name != "" && $name[0] == ':') {
        $usernameSearch = true;
        if (!validateName(substr($name, 1), true)) {
            $validationErrors[] = "name";
        } else {
            $username = substr($name, 1);
        }
    } else {
        if ($name != "*" && !validateSplitName($name, true)) {
            $validationErrors[] = "name";
        }
    }
    if (count($validationErrors) > 0) {
        http_response_code(400);
        echo json_encode($validationErrors);
        die();
    }

    if ($usernameSearch) {
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
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE username = '$username'
            AND DATE(punch_time) <= '$endDate'
            AND DATE(punch_time) >= '$startDate'
            ORDER BY username DESC, punch_time DESC
EOT;
    } else if ($name == "*" || $name == "") {
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
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE DATE(punch_time) <= '$endDate'
            AND DATE(punch_time) >= '$startDate'
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
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE last_name = '$lastName'
            AND first_name = '$firstName'
            AND DATE(punch_time) <= '$endDate'
            AND DATE(punch_time) >= '$startDate'
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

        if ($next['volunteer_id'] != $row['volunteer_id']) {
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
        $timeDiff = $startTime->diff($stopTime);
        $hours = $timeDiff->d * 24 + $timeDiff->h + ($timeDiff->i / 60);

        $hoursAcc += $groupSize * $hours;
        if($communityService) {
            $csHoursAcc += $groupSize * $hours;
        }
        $volunteersAcc += $groupSize;

        $responseRow = [];
        $responseRow['id'] = $row['volunteer_id'];
        $responseRow['date'] = $startTime->format("m-d-Y");
        $responseRow['name'] = formatName($row['last_name'] . ', ' . $row['first_name']);
        $responseRow['username'] = $row['username'];
        $responseRow['department'] = $row['department_name'];
        $responseRow['assignment'] = $row['assignment_name'];
        $responseRow['groupSize'] = $groupSize;
        $responseRow['communityService'] = $communityService ? 'Yes' : 'No';
        $responseRow['in'] = $startTime->format("h:i A");
        $responseRow['out'] = $stopTime->format("h:i A");
        $responseRow['hours'] = number_format($hours, 2, '.', '');
        $response[] = $responseRow;

        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    $reportFinal['tdata'] = $response;
    $reportFinal['totalVolunteers'] = $volunteersAcc;
    $reportFinal['uniqueVolunteers'] = count($volunteersSet);
    $reportFinal['communityServiceHours'] = number_format($csHoursAcc, 2, '.', '');
    $reportFinal['nonCommunityServiceHours'] = number_format($hoursAcc - $csHoursAcc, 2, '.', '');
    $reportFinal['totalHours'] = number_format($hoursAcc, 2, '.', '');

    http_response_code(200);
    echo json_encode($reportFinal);
}

mysqli_close($dbc);
?>