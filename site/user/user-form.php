<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
}

require_once('../global/mysqli_connect.php');
require_once('../global/utils.php');

$username = $_SESSION['username'];
$query = "SELECT volunteer_id, last_name, first_name FROM volunteers WHERE username = '{$username}';";
$result = mysqli_query($dbc, $query);
if (!$result) {
    die($query."<br/><br/>".mysqli_error($dbc));
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$lastName = formatName($row['last_name']);
$firstName = formatName($row['first_name']);
$volunteerId = $row['volunteer_id'];

$punchedIn = true;
$currentDepartment;
$currentAssignment;
$currentGroupSize;
$query = "SELECT punch_time, punch_type, department_id, assignment_id, group_size FROM events WHERE volunteer_id = '{$volunteerId}' ORDER BY punch_time DESC;";
$result = mysqli_query($dbc, $query);
if (!$result) {
    die($query."<br/><br/>".mysqli_error($dbc));
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
if (!$row || $row['punch_type'] == "punch-out") {
    $punchedIn = false;
} else {
    $currentDepartment = $row['department_id'];
    $currentAssignment = $row['assignment_id'];
    $currentGroupSize = $row['group_size'];
}

$departments = [];
$assignments = [];

$query = "SELECT department_id, department_name FROM departments";
$result = mysqli_query($dbc, $query);
if (!$result) {
    die($query."<br/><br/>".mysqli_error($dbc));
}
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $departments[$row['department_id']] = $row['department_name'];
}

$query = "SELECT assignment_id, assignment_name FROM assignments";
$result = mysqli_query($dbc, $query);
if (!$result) {
    die($query."<br/><br/>".mysqli_error($dbc));
}
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $assignments[$row['assignment_id']] = $row['assignment_name'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="user-form.css">
</head>

<body>
    <div id="content">
        <div class="row" id="sub-container">
            <div class="column">
                <form action="user-form-filter.php" class="section" id="date-selection">
                    <div>Showing results for</div>
                    <input type="date" name="start-date" id="start-date">
                    <div>to</div>
                    <input type="date" name="stop-date" id="stop-date">
                    <input type="submit" value="Refresh" id="refresh">
                </form>
                <div class="section">
                    <table>
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Department</th>
                                <th>Assignment</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody id="result-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="column" id="rhs-container">
                <div class="section column center" id="rhs">
                    <h1 id="user-welcome"></h1>
                    <div id="punch-in-container">
                        <form id="punch-in-form" class="column center" action="user_punch_in.php" method="post">
                            <select name="department" id="department-select">
                            <option value="default" disabled selected>Select your department</option>
                            <?php
                            foreach ($departments as $id => $name) {
                                echo '<option value="' . $id . '">' . $name . '</option>';
                            }
                            ?>
                            </select>
                            <select name="assignment" id="assignment-select">
                                <option value="default" disabled selected>Select your assignment</option>
                                <?php
                                foreach ($assignments as $id => $name) {
                                    echo '<option value="' . $id . '">' . $name . '</option>';
                                }
                                ?>
                            </select>
                            <div class="row">
                                <div>Group size:</div>
                                <input type="text" name="group-size" id="group-size-text" class="input-item" placeholder="#" autocomplete="off">
                            </div>
                            <input type="submit" class="submit-button" id="punch-in-button" value="Punch In">
                        </form>
                    </div>
                    <div id="punch-out-container">
                        <form id="punch-out-form" class="column center" action="user_punch_out.php" method="post">
                            <div class="text-med" id="current-department"></div>
                            <div class="text-med" id="current-assignment"></div>
                            <input type="submit" class="submit-button" id="punch-out-button" value="Punch Out">
                        </form>
                    </div>       
                    <div id="logout">Logout</div>
                </div>
            </div>
        </div>
    </div>
    <script src="jquery-3.1.1.min.js"></script>
    <script src="moment.js"></script>
    <?php
    $punchedInInt = (int)$punchedIn;
    echo <<<EOT
    <script>
        var volunteerLastName = '{$lastName}';
        var volunteerFirstName = '{$firstName}';
        var punchedIn = $punchedInInt;
    </script>
EOT;
    ?>
    <script src="validate.js"></script>
    <script src="user-form.js"></script>
</body>

</html>