<?php

require_once 'DB.php';
session_start();

if(isset($_SESSION['username'])) {
    if($_SESSION['role'] === "doctor") {
        header('Location: homepage_doctors.php');
    } else if ($_SESSION['role'] === "patient") {
        header('Location: homepage_patients.php');
    }
    exit;
}

function validate_registration_form(string $username, string $email, string $password, string $first_name, string $last_name, string $ucn, string $address, string $phone_number)
{
    $errors = [];

    $validate_username = validate_username($username, 'patients');

    if($validate_username) {
        $errors['username'] = 'Това потребителско име е заето, изберете друго.';
    }

    $validate_email = validate_email($email, 'patients');

    if($validate_email) {
        $errors['email'] = 'Този имейл е зает, изберете друг.';
    }

    // Validate username
    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors['username'] = "Потребителското име трябва да бъде между 3 и 20 символа.";
    }

    // Validate password
    $password_pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%&*]).{8,}$/";
    if (!preg_match($password_pattern, $password)) {
        $errors['password'] = "Паролата трябва да бъде поне 8 символа,да съдържа поне една голяма и една малка буква, цифра, и един специален символ (@, #, $, %, &, *).";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Имейлът трябва да бъде в следния формат: example@example.com.";
    }

    // Validate UCN
    if (!preg_match("/^\d{10}$/", $ucn)) {
        $errors['ucn'] = "ЕГН-то трябва да бъде точно 10 символа.";
    }

    // Validate phone number
    if (!preg_match("/^\d{10}$/", $phone_number)) {
        $errors['phone_number'] = "Телефонният номер трябва да бъде точно 10 символа.";
    }
    
    return $errors;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $ucn = $_POST["ucn"];
    $address = $_POST["address"];
    $phone_number = $_POST["phone_number"];

    $errors = validate_registration_form($username, $email, $password, $first_name, $last_name, $ucn, $address, $phone_number);

    if(count($errors) === 0) {
        $password = sha1($_POST['password']);

        create_patient($username, $password, $email, $first_name, $last_name, $ucn, $address, $phone_number);

       $_SESSION['username'] = $username;
       $_SESSION['role'] = "patient";
        header('Location: homepage_patients.php');
    }
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
    <form class="registration-form" action="./registration_patient.php" method="POST">
        <div class="container-form">

            <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>

            <h2>Регистрация</h2>

            <div class="form-group">
                <label for="username">Потребителско име:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
            </div>
            <div class="error"> <?php if(isset($errors["username"])) { echo 'Потребителското име е невалидно.'; } ?> </div>

            <div class="form-group">
                <label for="first_name">Име:</label>
                <input type="text" id="first_name" name="first_name" required value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Имейл:</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            <div class="error"> <?php if(isset($errors["email"])) { echo 'Имейлът е невалиден.'; } ?> </div>

            <div class="form-group">
                <label for="ucn">ЕГН:</label>
                <input type="text" id="ucn" name="ucn" required value="<?php echo isset($_POST['ucn']) ? $_POST['ucn'] : ''; ?>">
            </div>
            <div class="error"> <?php if(isset($errors["ucn"])) { echo 'ЕГН-то е невалидно.'; } ?> </div>

            <div class="form-group">
                <label for="password">Парола:</label>
                <input type="password" id="password" name="password" required value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
            </div>
            <div class="error"> <?php if(isset($errors["password"])) { echo 'Паролата е невалидна.'; } ?> </div>

            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" required value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="phone_number">Телефонен номер:</label>
                <input type="tel" id="phone_number" name="phone_number" required value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">
            </div>
            <div class="error"> <?php if(isset($errors["phone_number"])) { echo 'Тел. номер е невалиден.';}?> </div>

            <div class="form-group">
                <label for="address">Адрес:</label>
                <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>">
            </div>

            <div class="full-span">
                <?php

                if(isset($errors) && count($errors)) {
                    foreach($errors as $error) {
                        echo '<div class="error">' . $error . '</div>';
                    }
                }

                ?>
            </div>

            <div class="full-span">
                <input type="submit" value="Регистрация">
            </div>

        </div>
    </form>

</body>
</html>