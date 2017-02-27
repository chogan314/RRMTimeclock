<?php
require_once('mysqli_connect.php');
require_once('utils.php');

function printArray($array)
{
    foreach ($array as $key => $value) {
        echo "{$key}: {$value}......";
    }
}

function calcHours($punches) {
    $punchStatus = 'punch-out';
    $punchInTime = 0;
    $timeAccumulator = 0;
    $lastName = '';
    $firstName = '';

    foreach($punches as $punchRecord) {
        if ($punchRecord['punch_type'] == $punchStatus) {
            continue;
        }

        if ($punchRecord['punch_type'] == 'punch-in') {
            $punchInTime = date_timestamp_get(date_create($punchRecord['punch_time']));
            $punchStatus = 'punch-in';
        }

        if ($punchRecord['punch_type'] == 'punch-out') {
            $punchOutTime = date_timestamp_get(date_create($punchRecord['punch_time']));
            $timeAccumulator += ($punchOutTime - $punchInTime) / 3600;
        }

        $lastName = $punchRecord['last_name'];
        $firstName = $punchRecord['first_name'];
    }

    return ['name' => "{$lastName}, {$firstName}", 'clockedTime' => $timeAccumulator];
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $name = sanitizeInput(getGetParam("name"), $dbc);
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $endDate = sanitizeInput(getGetParam("end-date"), $dbc);

    $query = '';

    if ($name != '*') {
        $pieces = explode(",", $name);
        $lastName = trim($pieces[0]);
        $firstName = trim($pieces[1]);
        
        // $query = "SELECT volunteer_id, first_name, last_name, punch_type, punch_time " .
        //     "FROM events INNER JOIN volunteers " .
        //     "ON events.volunteer_id=volunteers.volunteer_id " .
        //     "WHERE last_name='{$lastName}' " .
        //     "AND first_name='{$firstName}' " .
        //     "AND punch_time > '{$startDate}' " .
        //     "AND punch_time < '{$endDate}' " .
        //     "ORDER BY punch_time ASC";

        $query = <<<EOT
            SELECT
                volunteers.volunteer_id,
                last_name,
                first_name,
                punch_type,
                punch_time
            FROM events INNER JOIN volunteers 
            ON events.volunteer_id = volunteers.volunteer_id
            WHERE last_name = '$lastName'
            AND first_name = '$firstName'
            AND punch_time < '$endDate'
            AND punch_time > '$startDate'
            ORDER BY punch_time ASC
EOT;
    } else {
        $query = <<<EOT
            SELECT
                volunteers.volunteer_id,
                last_name,
                first_name,
                punch_type,
                punch_time
            FROM events INNER JOIN volunteers 
            ON events.volunteer_id = volunteers.volunteer_id 
            WHERE punch_time < '$endDate'
            AND punch_time > '$startDate'
            ORDER BY punch_time ASC
EOT;
    }

    $result = mysqli_query($dbc, $query);
    $punchDict = []; 

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $volunteerID = $row['volunteer_id'];
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $punchType = $row['punch_type'];
        $punchTime = $row['punch_time'];

        $punchDict[$volunteerID][] = ['first_name' => $firstName, 'last_name' => $lastName, 'punch_type' => $punchType, 'punch_time' => $punchTime];
    }

    $response = [];

    foreach($punchDict as $punches) {
        $response[] = calcHours($punches);
    }

    echo json_encode($response);
}

mysqli_close($dbc);
?>