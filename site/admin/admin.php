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
    <title>Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div id="content">
        <div>
            <div class="section column" id="links">
                <a href="report/hours-report.php" class="input-item">View Report</a>
                <a href="volunteer-view/volunteer-view.php" class="input-item">Edit Volunteers</a>
                <a href="hours-view/hours-view.php" class="input-item">Edit Hours</a>
                <a href="departments-view/departments-view.php" class="input-item">Edit Departments</a>
                <a href="assignments-view/assignments-view.php" class="input-item">Edit Assignments</a>
                <?php
                    if ($_SESSION["admin-level"] >= 5) {
                        echo '<a href="admin-view/admins-view.php" class="input-item">Edit Admins</a>';
                    }
                ?>
                <a href="admin-signout.php" class="input-item">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>