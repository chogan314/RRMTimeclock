<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');
require_once('dbutils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    /*
        $startDate: non-empty, valid date format
        $stopDate: non-empty, valid date format
        $name: non-empty, (x, y OR *)
    */
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $stopDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $name = sanitizeInput(getGetParam("name"), $dbc);

    $validationErrors = [];
    if (!validateDate($startDate)) {
        $validationErrors[] = "startDate";
    }
    if (!validateDate($stopDate)) {
        $validationErrors[] = "stopDate";
    }
    if ($name != "*" && !validateSplitName($name, true)) {
        $validationErrors[] = "name";
    }
    if (count($validationErrors) > 0) {
        http_response_code(400);
        echo json_encode($validationErrors);
        die();
    }

    $query = '';

    if ($name == '*' || $name == '') {
        $query = <<<EOT
            SELECT
                event_id,
                punch_time,
                last_name,
                first_name,
                username,
                events.community_service,
                group_size,
                punch_type,
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE DATE(punch_time) <= '$stopDate'
            AND DATE(punch_time) >= '$startDate'
            ORDER BY username DESC, punch_time DESC, punch_type DESC;
EOT;
    } else {
        if (count(explode(",", $name)) != 2) {
            http_response_code(200);
            echo "Invalid name format";
            die();
        }

        $splitName = splitName($name);
        $lastName = $splitName[0];
        $firstName = $splitName[1];

        $query = <<<EOT
            SELECT
                event_id,
                punch_time,
                last_name,
                first_name,
                username,
                events.community_service,
                group_size,
                punch_type,
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE DATE(punch_time) <= '$stopDate'
            AND DATE(punch_time) >= '$startDate'
            AND first_name = '$firstName'
            AND last_name = '$lastName'
            ORDER BY username DESC, punch_time DESC, punch_type DESC;
EOT;
    }

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $rows = [];
    $userData = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rows[] = $row;
        $userData[$row['username']][] = $row;
    }

    $invalidUsers = [];
    foreach ($userData as $punchRecords) {
        if (!verifyPunches($punchRecords)) {
            $invalidUsers[] = $punchRecords['username'];
        }
    }

    $response = [];
    $response['invalidUsers'] = $invalidUsers;

    foreach ($rows as $row) {
        $dateTime = new DateTime($row['punch_time']);
        $punch = $row['punch_type'] == "punch-in" ? "In" : "Out";

        $responseRow = [];
        $responseRow['id'] = $row['event_id'];
        $responseRow['date'] = $dateTime->format("m-d-Y");
        $responseRow['name'] = formatName($row['last_name'] . ', ' . $row['first_name']);
        $responseRow['username'] = $row['username'];
        $responseRow['group-size'] = $row['group_size'];
        $responseRow['community-service'] = $row['community_service'] ? 'Yes' : 'No';
        $responseRow['punch-type'] = $punch;
        $responseRow['time'] = $dateTime->format("h:i A");
        $responseRow['department'] = $row['department_name'];
        $responseRow['assignment'] = $row['assignment_name'];
        $response['tdata'][] = $responseRow;
    }
}

http_response_code(200);
echo json_encode($response);

mysqli_close($dbc);
?>