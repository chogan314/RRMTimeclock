<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $startDate = sanitizeInput(getGetParam("start-date"), $dbc);
    $endDate = sanitizeInput(getGetParam("stop-date"), $dbc);
    $username = $_SESSION['username'];

    $query = <<<EOT
        SELECT
            punch_type,
            punch_time,
            department_name,
            assignment_name
        FROM events
            INNER JOIN volunteers ON events.volunteer_id = volunteers.volunteer_id
            INNER JOIN departments ON events.department_id = departments.department_id
            INNER JOIN assignments ON events.assignment_id = assignments.assignment_id
        WHERE username = '$username'
        AND punch_time <= '$endDate'
        AND punch_time >= '$startDate'
        ORDER BY punch_time DESC
EOT;

    $result = mysqli_query($dbc, $query);
    if (!$result) {
        die($query."<br/><br/>".mysqli_error($dbc));
    }

    $response = [];
    $hoursAcc = 0;
    $row = $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    while ($row) {
        $responseRow = [];
        if ($row['punch_type'] != 'punch-out') {
            $time = new DateTime($row['punch_time']);
            $responseRow['event'] = 'Punch In';
            $responseRow['department'] = $row['department_name'];
            $responseRow['assignment'] = $row['assignment_name'];
            $responseRow['date'] = $time->format("m-d-Y");
            $responseRow['time'] = $time->format("h:i A");
            $responseRow['hours'] = '';
            $response[] = $responseRow;
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            continue;
        }

        $next = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (!$next) {
            break;
        }

        $startTime = new DateTime($next['punch_time']);
        $stopTime = new DateTime($row['punch_time']);
        $timeDiff = $startTime->diff($stopTime);
        $hours = $timeDiff->h + ($timeDiff->i / 60);
        $hoursAcc += $hours;

        $responseRow['event'] = 'Punch Out';
        $responseRow['department'] = $row['department_name'];
        $responseRow['assignment'] = $row['assignment_name'];
        $responseRow['date'] = $stopTime->format("m-d-Y");
        $responseRow['time'] = $stopTime->format("h:i A");
        $responseRow['hours'] = number_format($hours, 2, '.', '');
        $response[] = $responseRow;

        $row = $next;
    }

    $reportFinal['tData'] = $response;
    $reportFinal['totalHours'] = number_format($hoursAcc, 2, '.', '');

    http_response_code(200);
    echo json_encode($reportFinal);
}
mysqli_close($dbc);
?>