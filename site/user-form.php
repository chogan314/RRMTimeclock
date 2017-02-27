<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
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
                <form action="get_volunteer_hours.php" class="section" id="date-selection">
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
                                <th>Assignment</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody id="result-body">
                            <tr>
                                <td>Punch In</td>
                                <td>Service Service Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Punch Out</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>5.45</td>
                            </tr>
                            <tr>
                                <td>Punch In</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Punch Out</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>8.29</td>
                            </tr>
                            <tr>
                                <td>Punch In</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Punch Out</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>5.45</td>
                            </tr>
                            <tr>
                                <td>Punch In</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Punch Out</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>8.29</td>
                            </tr>
                            <tr>
                                <td>Punch In</td>
                                <td>Service</td>
                                <td>1/29/17</td>
                                <td>9:00</td>
                                <td>-</td>
                            </tr>
                            <tr id="total-hours">
                                <td>Total Hours:</td>
                                <td class="text-right" colspan="4">200.82</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="column" id="rhs-container">
                <div class="section" id="rhs">
                    <h1>Welcome, Your Name Here.</h1>
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
    <script src="user-form.js"></script>
</body>

</html>