<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
}

require_once('mysqli_connect.php');
require_once('utils.php');

$username = $_SESSION['username'];
$query = "SELECT volunteer_id, last_name, first_name FROM volunteers WHERE username = '{$username}';";
$result = mysqli_query($dbc, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$lastName = $row['last_name'];
$firstName = $row['first_name'];
$volunteerId = $row['volunteer_id'];

$punchedOut = true;
$query = "SELECT punch_type FROM events WHERE volunteer_id = '{$volunteerId}';";
$result = mysqli_query($dbc, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
if (!$row || $row['punch_type'] == "punch-out") {
    $punchedOut = false;
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
                <div class="section" id="rhs">
                    <h1 id="user-welcome">Welcome, Your Name Here.</h1>
                    <form action="volunteer_punch.php" method="post">
                        <select name="cars" id="role-select">
                            <option value="" disabled selected>Select your assignment</option>
                            <!--todo: get assignments-->
                            <option value="volvo">Volvo</option>
                            <option value="saab">Saab</option>
                            <option value="fiat">Fiat</option>
                            <option value="audi">Audi</option>
                        </select>
                        <!--<input type="text" class="input-item" value="test">-->
                        <div class="submit-button" id="punch-in-button">Punch In</div>
                        <div class="submit-button" id="punch-out-button">Punch Out</div>
                    </form>
                    <div id="logout">Logout</div>
                </div>
            </div>
        </div>
    </div>
    <script src="jquery-3.1.1.min.js"></script>
    <script src="moment.js"></script>
    <?php
    echo <<<EOT
    <script>
        var volunteerLastName = '{$lastName}';
        var volunteerFirstName = '{$firstName}';
        var puchedOut = {$punchedOut};
    </script>
EOT;
    ?>
    <script src="user-form.js"></script>
</body>

</html>