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
    <title>Departments View</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="departments-view.css">
</head>
<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="admin.php" class="input-button">Back</a>
                <a href="admin-signout.php" class="input-button">Signout</a>
            </div>
            <form action="departments-view-create.php" class="row section create-record-section" id="create-form">
                <div>Create new department record:</div>
                <input class="input-item" type="text" name="department-name" id="record-name" placeholder="Department name">
                <input class="input-button" type="submit" value="Create">
            </form>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Department Name</th>
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
                <form action="departments-view-update.php" class="section popup-form" id="popup-form">
                    <div class="row">
                        <div class="column">
                            <h1 id="popup-edit-header">Update Department Name</h1>
                            <input type="text" name="id" id="popup-record-id">
                            <input class="input-item" type="text" name="department-name" autocomplete="off" placeholder="Department name" id="popup-department-name-input">
                            
                            <input type="submit" class="input-button" value="Update">
                            <div class="input-button" id="popup-cancel-button">Cancel</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="jquery-3.1.1.min.js"></script>
    <script src="departments-view.js"></script>
</body>
</html>