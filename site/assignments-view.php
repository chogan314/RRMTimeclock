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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Assignments View</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assignments-view.css">
</head>
<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="admin.php" class="input-button">Back</a>
                <a href="admin-signout.php" class="input-button">Signout</a>
            </div>
            <form action="assignments-view-create.php" class="row section create-record-section" id="create-form">
                <div>Create new assignment record:</div>
                <input class="input-item" type="text" name="assignment-name" id="record-name" placeholder="Assignment name" autocomplete="off">
                <input class="input-button" type="submit" value="Create">
            </form>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Assignment Name</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody id="result-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="popup-form-container" id="popup">
        <div class="popup-form-content">
            <div class="popup-form-wrapper">
                <form action="assignments-view-update.php" class="section popup-form" id="popup-form">
                    <div class="row">
                        <div class="column">
                            <h1 id="popup-edit-header">Update Assignment Name</h1>
                            <input type="text" name="id" id="popup-record-id" autocomplete="off" readonly>
                            <input class="input-item" type="text" name="assignment-name" placeholder="Assignment name" id="popup-assignment-name-input" autocomplete="off">
                            
                            <input type="submit" class="input-button" value="Update">
                            <div class="input-button" id="popup-cancel-button">Cancel</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="jquery-3.1.1.min.js"></script>
    <script src="assignments-view.js"></script>
</body>
</html>