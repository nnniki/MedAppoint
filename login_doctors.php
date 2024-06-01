<?php

require_once 'DB.php';
session_start();

//if(isset($_SESSION['email'])) {
//    header('Location: homepage.php');
//    exit;
//}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = login($username, $password, 'doctors');

    if($result) {
//        $_SESSION['username'] = $username;
        header('Location: homepage_logged.php');
        exit;
    }

    $error = 'Wrong email or password!';
}

?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Patient Registration Form</title>
    <link rel="stylesheet" type="text/css" href="./css/registration.css">
</head>
<body>
<form class="registration-form" action="./login_doctors.php" method="POST">
    <div class="container-form">

        <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>

        <h2>Вход</h2>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="full-span">
            <input type="submit" value="Вход">
        </div>

        <?php

        if(isset($error)) {
            echo '<div class="error">' . $error . '</div>';
        }

        ?>

    </div>
</form>

</body>
</html>