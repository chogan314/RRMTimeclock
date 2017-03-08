<?php
// session_start();
// if (!isset($_SESSION['admin-username'])) {
//     header("Location: admin-signin.html");
//     exit();
// }

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
            <form action="assignments-view-create.php" class="row section create-record-section" id="create-form">
                <div>Create new assignment record:</div>
                <input class="input-item" type="text" name="assignment-name" id="record-name" placeholder="Assignment name">
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
                            <input type="text" name="id" id="popup-record-id">
                            <input class="input-item" type="text" name="assignment-name" autocomplete="off" placeholder="Assignment name" id="popup-assignment-name-input">
                            
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