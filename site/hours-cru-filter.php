<?php
// session_start();
// if (!isset($_SESSION['admin-username'])) {
//     header("Location: admin-signin.html");
//     exit();
// }

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    /*
        $startDate: non-empty, valid date format
        $stopDate: non-empty, valid date format
        $name: non-empty, (x, y OR *)
    */
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $stopDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $name = sanitizeInput(getGetParam("name"), $dbc);

    $query = '';

    if ($name == '*' || $name == '') {
        $query = <<<EOT
            SELECT
                event_id,
                punch_time
                last_name,
                first_name,
                username,
                group_size,
                punch_type,
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE punch_time <= '$stopDate'
            AND punch_time >= '$startDate'
            ORDER BY punch_time DESC;
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
                punch_time
                last_name,
                first_name,
                username,
                group_size,
                punch_type,
                department_name,
                assignment_name
            FROM events
                INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
                INNER JOIN departments ON events.department_id = departments.department_id
                INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
            WHERE punch_time <= '$stopDate'
            AND punch_time >= '$startDate'
            AND first_name = '$firstName'
            AND last_name = '$lastName'
            ORDER BY username DESC, punch_time DESC;
EOT;
    }

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $dateTime = new DateTime($row['punch_time']);

        $responseRow = [];
        $responseRow['id'] = $row['event_id'];
        $responseRow['date'] = $dateTime->format("m-d-Y");
        $responseRow['name'] = $row['last_name'] . ', ' . $row['first_name'];
        $responseRow['username'] = $row['username'];
        $responseRow['group-size'] = $row['group_size'];
        $responseRow['community-service'] = $row['community_service'] ? 'Yes' : 'No';
        $responseRow['punch-type'] = $row['punch-type'];
        $responseRow['time'] = $startTime->format("h:i A");
        $responseRow['department'] = $row['department_name'];
        $responseRow['assignment'] = $row['assignment_name'];
        $response[] = $responseRow;
    }
}

http_response_code(200);
echo json_encode($response);

mysqli_close($dbc);
?>