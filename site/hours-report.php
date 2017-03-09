<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="hours-report.css">
</head>

<body>
    <div id="content">
        <div class="column">
            <form class="section row row-form" id="filter-form" action="hours-report-get.php" method="get">
                <div>Showing results for</div>
                <input type="date" name="start-date" class="input-item" id="start-date">
                <div>to</div>
                <input type="date" name="stop-date" class="input-item" id="stop-date">
                <div>for</div>
                <input list="names-list" name="name" class="input-item" id="names-input" autocomplete="off" placeholder="Lastname, Firstname"></input>
                <datalist id="names-list">
                    <!--TODO: get list of names from DB-->
                    <option value="Chrome">
                    <option value="Firefox">
                    <option value="Internet Explorer">
                    <option value="Opera">
                    <option value="Safari">
                    <option value="Microsoft Edge">
                </datalist>
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
                            <th>Group Size</th>
                            <th>Community Service</th>
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
    <script src="jquery-3.1.1.min.js"></script>
    <script src="hours-report.js"></script>
</body>

</html>