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
    <title>Volunteers View</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="volunteer-view.css">
</head>
<body>
    <div id="content">
        <div class="column">
            <div class="row section row-menu">
                <a href="admin.php" class="input-button">Back</a>
                <a href="admin-signout.php" class="input-button">Signout</a>
            </div>
            <form action="volunteer-view-filter.php" class="row section row-form" id="filter-form">
                <div>Showing results for</div>
                <input list="names-list" name="name" class="input-item" id="names-input" autocomplete="off" placeholder="Lastname, Firstname"></input>
                <datalist id="names-list" autocomplete="off">
                </datalist>
                <input type="submit" value="Refresh" class="input-button" id="refresh">
            </form>
            <div class="row section create-record-section">
                <div>Create new volunteer record:</div>
                <div class="input-button" id="open-create-popup">Create</div>
            </div>
            <div class="section">
                <table class="hide-first-column">
                    <thead>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Community Service</th>
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
                            <h1 id="popup-create-header">Create New Volunteer</h1>
                            <h1 id="popup-edit-header">Update Volunteer</h1>
                            <input type="text" name="id" id="popup-record-id" autocomplete="off" readonly>

                            <!--Last name input id="popup-lastname-input"-->
                            <input class="input-item" type="text" name="last-name" autocomplete="off" placeholder="Last name" id="popup-lastname-input">
                            
                            <!--Last name input id="popup-firstname-input"-->
                            <input class="input-item" type="text" name="first-name" autocomplete="off" placeholder="First name" id="popup-firstname-input">
                            
                            <!--Community service checkboc id="popup-community-service-cb"-->
                            <div class="row"><div>Community Service:</div><input type="checkbox" value="1" name="community-service"id="popup-community-service-cb"></div>
                            
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
    <script src="volunteer-view.js"></script>
</body>
</html>