<!--TODO: get list of names and usernames-->
<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: ../admin-signin.php");
    exit();
}

require_once('../../global/mysqli_connect.php');
require_once('../../global/utils.php');

$departments = [];
$departmentIds = [];
$assignments = [];
$assignmentIds = [];

$query = "SELECT department_id, department_name from departments";
$result = $result = mysqli_query($dbc, $query);

if ($result) {
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $departments[$row['department_id']] = $row['department_name'];
        $departmentIds[$row['department_name']] = $row['department_id'];
    }
}

$query = "SELECT assignment_id, assignment_name from assignments";
$result = $result = mysqli_query($dbc, $query);

if ($result) {
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $assignments[$row['assignment_id']] = $row['assignment_name'];
        $assignmentIds[$row['assignment_name']] = $row['assignment_id'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hours View</title>
    <link rel="stylesheet" href="../../global/style.css">
    <link rel="stylesheet" href="hours-view.css">
</head>
<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="../admin.php" class="input-button">Back</a>
                <a href="../admin-signout.php" class="input-button">Signout</a>
            </div>
            <form action="hours-view-filter.php" class="row section row-form" id="filter-form">
                <div>Showing results for</div>
                <input type="date" name="start-date" class="input-item" id="start-date">
                <div>to</div>
                <input type="date" name="stop-date" class="input-item" id="stop-date">
                <div>for</div>
                <input list="names-list" name="name" class="input-item" id="names-input" autocomplete="off" placeholder="Lastname, Firstname"></input>
                <datalist id="names-list" autocomplete="off">
                </datalist>
                <input type="submit" value="Refresh" class="input-button" id="refresh">
            </form>
            <div class="row section create-record-section">
                <div>Create new punch record:</div>
                <div class="input-button" id="open-create-popup">Create</div>
            </div>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th class="column-narrow">Group Size</th>
                            <th class="column-narrow">Community Service</th>
                            <th>Punch</th>
                            <th>Time</th>
                            <th>Department</th>
                            <th>Assignment</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody id="result-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--Popup Form-->
    <div class="popup-form-container" id="popup">
        <div class="popup-form-content">
            <div class="popup-form-wrapper">
                <form action="" method="post" class="section popup-form" id="popup-form">
                    <div class="row">
                        <div class="column">
                            <h1 id="popup-create-header" align="center">Create Punch Record</h1>
                            <h1 id="popup-edit-header" align="center">Update Punch Record</h1>
                            <input type="text" name="record-id" id="popup-record-id" autocomplete="off" readonly></input>
                            <!--Date Input id="popup-date-input"-->
                            <div class="row"><div>Date:</div><input class="input-item" type="date" id="popup-date-input" name="date"></div>
                            
                            <!--Name Input id="popup-name-input"-->
                            <input list="popup-names-list" name="name" class="input-item" autocomplete="off" placeholder="Lastname, Firstname"  id="popup-name-input"></input>
                            <datalist id="popup-names-list" autocomplete="off">
                            </datalist>

                            <!--Username Input id="popup-username-input"-->
                            <input list="popup-usernames-list" name="username" class="input-item" autocomplete="off" placeholder="Username" id="popup-username-input"></input>
                            <datalist id="popup-usernames-list" autocomplete="off">
                            </datalist>

                            <!--Community Service Input id="popup-cs-select"-->
                            <div class="row">
                                <div>Community Service:</div>
                                <select name="community-service" class="input-item" id="popup-cs-select">
                                    <option value="default" disabled selected>Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="use-default">Use volunteer default</option>
                                </select>
                            </div>
                            
                            <!--Group Size Input id="popup-group-size-input"-->
                            <input type="text" class="input-item" placeholder="Group size" name="group-size" id="popup-group-size-input" autocomplete="off">
                            
                            <!--Punch Type Input id="popup-punch-type-select"-->
                            <select name="punch-type" class="input-item" id="popup-punch-type-select">
                                <option value="default" disabled selected>Select record type</option>
                                <option value="In">Punch in</option>
                                <option value="Out">Punch out</option>
                            </select>

                            <!--Time Input id="popup-time-input"-->
                            <div class="row"><div>Time:</div><input class="input-item" type="time" name="time" id="popup-time-input"></div>

                            <!--Department Input id="popup-department-select"-->
                            <select name="department" class="input-item" id="popup-department-select">
                                <option value="default" disabled selected>Select department</option>
                                <?php
                                foreach ($departments as $id => $name) {
                                    echo '<option value="' . $id . '">' . $name . '</option>';
                                }
                                ?>
                            </select>

                            <!--Assignment Input id="popup-assignment-select"-->
                            <select name="assignment" class="input-item" id="popup-assignment-select">
                                <option value="default" disabled selected>Select assignment</option>
                                <?php
                                foreach ($assignments as $id => $name) {
                                    echo '<option value="' . $id . '">' . $name . '</option>';
                                }
                                ?>
                            </select>

                            <div class="input-button" id="popup-create-button">Create</div>
                            <div class="input-button" id="popup-edit-button">Update</div>
                            <div class="input-button" id="popup-cancel-button">Cancel</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="popup-form-container" id="popup-create-confirm">
        <div class="popup-form-content">
            <div class="popup-form-wrapper">
                <div class="section row">
                    <div class="column column-center">
                        <div>Record created.</div>
                        <div class="input-button" id="popup-create-confirm-close">Ok</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../global/jquery-3.1.1.min.js"></script>
    <?php
    $departmentIdsJSON = json_encode($departmentIds);
    $assignmentIdsJSON = json_encode($assignmentIds);
    echo <<<EOT
    <script>
        var departmentIds = {$departmentIdsJSON};
        var assignmentIds = {$assignmentIdsJSON};
    </script>
EOT
    ?>
    <script src="../../global/validate.js"></script>
    <script src="hours-view.js"></script>
</body>
</html>
