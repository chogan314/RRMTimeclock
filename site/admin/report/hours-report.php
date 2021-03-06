<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: ../admin-signin.php");
    exit();
}

require_once('../../global/mysqli_connect.php');
require_once('../../global/utils.php');

$departments = [];

$query = "SELECT department_id, department_name FROM departments";
$result = mysqli_query($dbc, $query);
if (!$result) {
    die($query."<br/><br/>".mysqli_error($dbc));
}
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $departments[$row['department_id']] = $row['department_name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link rel="stylesheet" href="../../global/style.css">
    <link rel="stylesheet" href="hours-report.css">
</head>

<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="../admin.php" class="input-button">Back</a>
                <a href="../admin-signout.php" class="input-button">Signout</a>
            </div>
            <form class="section row row-form" id="filter-form" action="hours-report-filter.php" method="get">
                <div>Showing results for</div>
                <input type="date" name="start-date" class="input-item" id="start-date">
                <div>to</div>
                <input type="date" name="stop-date" class="input-item" id="stop-date">
                <div>for</div>
                <input list="names-list" name="name" class="input-item" id="names-input" autocomplete="off" placeholder="Lastname, Firstname OR :username"></input>
                <datalist id="names-list" autocomplete="off">
                </datalist>
                <select name="department" class="input-item" id="department-select">
                    <option value="any" selected>Any department</option>
                    <?php
                    foreach ($departments as $id => $name) {
                        echo '<option value="' . $id . '">' . $name . '</option>';
                    }
                    ?>
                </select>
                <input type="submit" value="Refresh" class="input-button" id="refresh">
            </form>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Department</th>
                            <th>Assignment</th>
                            <th class="column-narrow">Group Size</th>
                            <th class="column-narrow">Community Service</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody id="result-body">
                    </tbody>
                </table>
            </div>
            <div class="section">
                <table id="totals-table">
                    <tbody>
                        <tr>
                            <td>Total volunteers:</td>
                            <td id="total-volunteers"></td>
                        </tr>
                            <td>Unique volunteers:</td>
                            <td id="unique-volunteers"></td>
                        </tr>
                            <td>Community service hours:</td>
                            <td id="cs-hours"></td>
                        </tr>
                            <td>Non-Community service hours:</td>
                            <td id="non-cs-hours"></td>
                        </tr>
                            <td>Total volunteer hours:</td>
                            <td id="total-volunteer-hours"></td>                            
                        </tr>
                    </tbody>
                <table>
            </div>
        </div>
    </div>
    <script src="../../global/jquery-3.1.1.min.js"></script>
    <script src="../../global/validate.js"></script>
    <script src="hours-report.js"></script>
</body>

</html>