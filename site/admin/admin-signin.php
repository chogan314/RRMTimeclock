<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin signin</title>
    <link rel="stylesheet" href="../global/style.css">
    <link rel="stylesheet" href="admin-signin.css">
</head>
<body>
    <div id="content">
        <div>
            <form action="admin-validation.php" class="section column" id="admin-signin-form">
                <h1>RRM Timesheet Admin Signin</h1>
                <input type="text" name="username" class="input-item" id="username" placeholder="Username" autocomplete="off">
                <input type="password" name="password" class="input-item" id="password" placeholder="Password" autocomplete="off">
                <input type="submit" class="input-button" value="Submit">
            </form>
        </div>
    </div>
    <div id="temp">
        <a href="../user/signin.html">To User Signin</a>
    </div>
    <script src="../global/jquery-3.1.1.min.js"></script>
    <script>
        var form = $('#admin-signin-form');
        $(form).submit(function(event) {
            event.preventDefault();
            var formData = $(form).serialize();

            $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData
            }).done(function(response) {
                window.location = response;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                //todo
            });
        });
    </script>
</body>
</html>