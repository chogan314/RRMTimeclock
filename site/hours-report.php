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
            <form class="section row row-form" id="filter-form" action="get_hours_report.php" method="get">
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
            <div class="section" id="test"></div>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Group</th>
                            <th>Service</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Hours</th>
                            <th>Assignment</th>
                        </tr>
                    </thead>
                    <tbody id="result-body">
                        <tr>
                            <td>2/1/17</td>
                            <td>Lastname, Firstnamealskjdflk</td>
                            <td>lFirst</td>
                            <td>1</td>
                            <td>No</td>
                            <td>9:00</td>
                            <td>5:00</td>
                            <td>8</td>
                            <td>Assignment</td>
                        </tr>
                        <tr>
                            <td>2/1/17</td>
                            <td>Last, First</td>
                            <td>lFirst</td>
                            <td>1</td>
                            <td>No</td>
                            <td>9:00</td>
                            <td>5:00</td>
                            <td>8</td>
                            <td>Assignment</td>
                        </tr>
                        <tr>
                            <td>2/1/17</td>
                            <td>Last, First</td>
                            <td>lFirst</td>
                            <td>1</td>
                            <td>No</td>
                            <td>9:00</td>
                            <td>5:00</td>
                            <td>8</td>
                            <td>Assignment</td>
                        </tr>
                        <tr>
                            <td>2/1/17</td>
                            <td>Last, First</td>
                            <td>lFirst</td>
                            <td>2</td>
                            <td>No</td>
                            <td>9:00</td>
                            <td>5:00</td>
                            <td>8</td>
                            <td>Assignment</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="section">
                <table id="totals-table">
                    <tbody>
                        <tr>
                            <td>Total volunteers:</td>
                            <td>500</td>
                        </tr>
                            <td>Unique volunteers:</td>
                            <td>500</td>
                        </tr>
                            <td>Community service hours:</td>
                            <td>500</td>
                        </tr>
                            <td>Non-Community service hours:</td>
                            <td>500</td>
                        </tr>
                            <td>Total volunteer hours:</td>
                            <td>500</td>                            
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