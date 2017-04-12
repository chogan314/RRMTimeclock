<?php
session_start();
if (!isset($_SESSION['admin-id'])) {
    header("Location: admin-signin.php");
    exit();
}

if (!isset($_SESSION['admin-level']) || $_SESSION['admin-level'] < 5) {
    header("Location: admin-signin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admins View</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admins-view.css">
</head>
<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="admin.php" class="input-button">Back</a>
                <a href="admin-signout.php" class="input-button">Signout</a>
            </div>
            <div class="row section create-record-section">
                <div>Create new admin record:</div>
                <div class="input-button" id="open-create-popup">Create</div>
            </div>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Admin Level</th>
                            <th>Username</th>
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
                <form action="" class="section popup-form" id="popup-form">
                    <div class="row">
                        <div class="column">
                            <h1 id="popup-create-header">Create New Admin</h1>
                            <h1 id="popup-edit-header">Update Admin</h1>
                            <input type="text" name="id" id="popup-record-id" autocomplete="off" readonly>

                            <!--Last name input id="popup-lastname-input"-->
                            <input class="input-item" type="text" name="last-name" autocomplete="off" placeholder="Last name" id="popup-lastname-input">
                            
                            <!--Last name input id="popup-firstname-input"-->
                            <input class="input-item" type="text" name="first-name" autocomplete="off" placeholder="First name" id="popup-firstname-input">
                            
                            <!--Last name input id="popup-admin-level-input"-->
                            <input class="input-item" type="text" name="admin-level" autocomplete="off" placeholder="Admin level" id="popup-admin-level-input">
                            
                            <!--Username input id="popup-username-input"-->
                            <input class="input-item" type="text" name="username" autocomplete="off" placeholder="Username" id="popup-username-input">
                            
                            <!--Password input id="popup-password-input"-->
                            <input class="input-item" type="text" name="password" autocomplete="off" placeholder="Password" id="popup-password-input">
                            
                            <div class="input-button" id="popup-create-button">Create</div>
                            <div class="input-button" id="popup-edit-button">Update</div>
                            <div class="input-button" id="popup-cancel-button">Cancel</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="jquery-3.1.1.min.js"></script>
    <script src="validate.js"></script>
    <script src="admins-view.js"></script>
</body>
</html>