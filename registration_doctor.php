<?php

require_once 'DB.php';
session_start();

// if(isset($_SESSION['email'])) {
//     header('Location: home.php');
// }

function validate_registration_form(string $username, string $email, string $password, string $first_name, string $last_name, string $ucn, string $address, string $region, string $phone_number, string $speciality)
{
    $errors = [];

    $validate_username = validate_username($username, 'doctors');

    if($validate_username) {
        $errors['username'] = 'This username is invalid, please choose another one!';
    }

    $validate_email = validate_email($email, 'doctors');

    if($validate_email) {
        $errors['email'] = 'This email is invalid, please choose another one!';
    }

    // Validate username
    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors['username'] = "Username must be between 3 and 20 characters.";  
    }

    // Validate password
    $password_pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=]).{8,}$/";
    if (!preg_match($password_pattern, $password)) {
        $errors['password'] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character (@, #, $, %, &, *).";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email must be in the format: example@example.com.";
    }

    // Validate UCN
    if (!preg_match("/^\d{10}$/", $ucn)) {
        $errors['ucn'] = "UCN must be exactly 10 digits.";
    }

    // Validate phone number
    if (!preg_match("/^\d{10}$/", $phone_number)) {
        $errors['phone_number'] = "Phone number must be exactly 10 digits.";
    }

    // Validate speciality
    $valid_specialities = array("кардиолог", "невролог", "УНГ", "уролог", "очен", "кожен", "гинеколог");
    if (!in_array($speciality, $valid_specialities)) {
        $errors["speciality"] = "Невалидна специалност. Моля избери някой измежду " . $valid_specialities;
    }

    $valid_regions = array("София", "Бургас", "Варна", "Пловдив");
    if (!in_array($speciality, $valid_specialities)) {
        $errors["region"] = "Невалиден регион. Моля избери някой измежду " . $valid_regions;
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
    $region = $_POST["region"];
    $phone_number = $_POST["phone_number"];
    $speciality = $_POST["speciality"];

    $errors = validate_registration_form($username, $email, $password, $first_name, $last_name, $ucn, $address, $region, $phone_number, $speciality);

     if(count($errors) === 0) {
         $password = sha1($_POST['password']);

         create_doctor($username, $password, $email, $first_name, $last_name, $ucn, $address, $region, $phone_number, $speciality);


//        $_SESSION['email'] = $email;
         header('Location: login_doctors.php');
     }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Doctor Registration Form</title>
    <link rel="stylesheet" type="text/css" href="./css/registration.css">
</head>
<body>
    <form class="registration-form" action="./registration_doctor.php" method="POST">
    <div class="container-form">

        <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>

        <h2>Регистрация</h2>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["username"])) { echo $errors["username"]; } ?> </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["password"])) { echo $errors["password"]; } ?> </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["email"])) { echo $errors["email"]; } ?> </div>

        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["first_name"])) { echo $errors["first_name"];}?> </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["last_name"])) { echo $errors["last_name"]; } ?> </div>

        <div class="form-group">
            <label for="ucn">UCN:</label>
            <input type="text" id="ucn" name="ucn" required value="<?php echo isset($_POST['ucn']) ? $_POST['ucn'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["ucn"])) { echo $errors["ucn"]; } ?> </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["address"])) { echo $errors["address"]; } ?> </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" required value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["phone_number"])) { echo $errors["phone_number"];}?> </div>

        <div class="full-span">
            <label for="speciality">Speciality:</label>
            <select id="speciality" name="speciality" required>
                <option value="кардиолог">кардиолог</option>
                <option value="невролог">невролог</option>
                <option value="УНГ">УНГ</option>
                <option value="уролог">уролог</option>
                <option value="гинеколог">гинеколог</option>
                <option value="очен">очен</option>
                <option value="кожен">кожен</option>
            </select>
        </div>
        <div class="error"> <?php if(isset($errors["speciality"])) { echo $errors["speciality"];}?> </div>

        <div class="full-span">
            <label for="region">Region:</label>
            <select id="region" name="region" required>
                <option value="София">София</option>
                <option value="Бургас">Бургас</option>
                <option value="Варна">Варна</option>
                <option value="Пловдив">Пловдив</option>
            </select>
        </div>
        <div class="error"> <?php if(isset($errors["region"])) { echo $errors["region"];}?> </div>

        <div class="full-span">
            <input type="submit" value="Регистрация">
        </div>

    </div>
    </form>

</body>
</html>