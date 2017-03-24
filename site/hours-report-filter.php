<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');
require_once('dbutils.php');

function mergePunches($punchRecords) {
    $ins = [];
    $outs = [];
    $size = count($punchRecords);
    for ($i = 0; $i < $size; $i++) {
        $record = $punchRecords[$i];
        if ($i == 0 && $record['punch_type'] == 'punch-in') {
            continue;
        }
        if ($i == $size - 1 && $record['punch_type'] == 'punch-out') {
            break;
        }
        if ($record['punch_type'] == 'punch-in') {
            $ins[] = $record;
        } else {
            $outs[] = $record;
        }
    }

    if (count($ins) != count($outs)) {
        http_response_code(500);
        echo "database error";
        die();
    }

    $mergedRecords = [];
    $totalVolunteers = 0;
    $csHours = 0;
    $totalHours = 0;

    $size = count($ins);
    for ($i = 0; $i < $size; $i++) {
        $in = $ins[$i];
        $out = $outs[$i];

        $groupSize = $in['group_size'];
        $communityService = $in['community_service'];
        $startTime = new DateTime($in['punch_time']);
        $stopTime = new DateTime($out['punch_time']);
        $seconds = $stopTime->getTimestamp() - $startTime->getTimestamp();
        $hours = $seconds / 60 / 60;

        $totalHours += $groupSize * $hours;
        if($communityService) {
            $csHours += $groupSize * $hours;
        }
        $totalVolunteers += $groupSize;

        $record = [];
        $record['id'] = $in['volunteer_id'];
        $record['date'] = $startTime->format("m-d-Y");
        $record['name'] = formatName($in['last_name'] . ', ' . $in['first_name']);
        $record['username'] = $in['username'];
        $record['department'] = $in['department_name'];
        $record['assignment'] = $in['assignment_name'];
        $record['groupSize'] = $groupSize;
        $record['communityService'] = $communityService ? 'Yes' : 'No';
        $record['in'] = $startTime->format("h:i A");
        $record['out'] = $stopTime->format("h:i A");
        $record['hours'] = number_format($hours, 2, '.', '');
        $mergedRecords[] = $record;
    }

    $dataAndRecords = [];
    $dataAndRecords['totalVolunteers'] = $totalVolunteers;
    $dataAndRecords['csHours'] = $csHours;
    $dataAndRecords['totalHours'] = $totalHours;
    $dataAndRecords['mergedRecords'] = $mergedRecords;
    return $dataAndRecords;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $endDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $name = sanitizeInput(getGetParam("name"), $dbc);
    $departmentId = sanitizeInput(getGetParam("department"), $dbc);
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
EOT;

    if ($usernameSearch) {
        $query .= " AND username = '$username'";
    } else if ($name != "*" && $name != "") {
        if (count(explode(",", $name)) != 2) {
            http_response_code(400);
            echo "invalid name format";
            die();
        }

        $splitName = splitName($name);
        $lastName = $splitName[0];
        $firstName = $splitName[1];
        $query .= " AND last_name = '$lastName' AND first_name = '$firstName'";
    }

    if ($departmentId !== "any") {
        $query .= " AND events.department_id = '$departmentId'";
    }

    $query .= " ORDER BY username ASC, punch_time DESC, punch_type DESC;";

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $userData = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $userData[$row['volunteer_id']][] = $row;
    }

    $invalidUsers = [];
    $totalVolunteers = 0;
    $totalHours = 0;
    $csHours = 0;
    $mergedRecords = [];
    foreach ($userData as $userRecords) {
        if (!verifyPunches($userRecords)) {
            $invalidUsers[] = $userRecords[0]['username'];
            continue;
        }
        $dataAndRecords = mergePunches($userRecords);
        $totalVolunteers += $dataAndRecords['totalVolunteers'];
        $totalHours += $dataAndRecords['totalHours'];
        $csHours += $dataAndRecords['csHours'];
        $mergedRecords = array_merge($mergedRecords, $dataAndRecords['mergedRecords']);
    }

    if (count($invalidUsers) > 0) {
        http_response_code(500);
        echo json_encode($invalidUsers);
        die();
    }

    $response['tdata'] = $mergedRecords;
    $response['totalVolunteers'] = $totalVolunteers;
    $response['uniqueVolunteers'] = count($userData);
    $response['communityServiceHours'] = number_format($csHours, 2, '.', '');
    $response['nonCommunityServiceHours'] = number_format($totalHours - $csHours, 2, '.', '');
    $response['totalHours'] = number_format($totalHours, 2, '.', '');

    http_response_code(200);
    echo json_encode($response);
}

mysqli_close($dbc);
?>