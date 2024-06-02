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


       $_SESSION['username'] = $username;
       $_SESSION['role'] = "doctor";
        header('Location: homepage_doctors.php');
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
        <div class="error"> <?php if(isset($errors["username"])) { echo 'Username is invalid.'; } ?> </div>

        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["first_name"])) { echo 'First name is invalid.';}?> </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["email"])) { echo 'Email is invalid.'; } ?> </div>

        <div class="form-group">
            <label for="ucn">UCN:</label>
            <input type="text" id="ucn" name="ucn" required value="<?php echo isset($_POST['ucn']) ? $_POST['ucn'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["ucn"])) { echo 'UCN is invalid.'; } ?> </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["password"])) { echo 'Password is invalid.'; } ?> </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["last_name"])) { echo 'Last name is invalid.'; } ?> </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" required value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["phone_number"])) { echo 'Phone number is invalid.';}?> </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>">
        </div>
        <div class="error"> <?php if(isset($errors["address"])) { echo 'Address is invalid.'; } ?> </div>

        <div class="full-span">
            <label for="speciality">Speciality:</label>
            <select id="speciality" name="speciality" required>
                <option value="кардиолог" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'кардиолог') ? 'selected' : ''; ?>>кардиолог</option>
                <option value="невролог" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'невролог') ? 'selected' : ''; ?>>невролог</option>
                <option value="УНГ" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'УНГ') ? 'selected' : ''; ?>>УНГ</option>
                <option value="уролог" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'уролог') ? 'selected' : ''; ?>>уролог</option>
                <option value="гинеколог" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'гинеколог') ? 'selected' : ''; ?>>гинеколог</option>
                <option value="очен" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'очен') ? 'selected' : ''; ?>>очен</option>
                <option value="кожен" <?php echo (isset($_GET['speciality']) && $_GET['speciality'] == 'кожен') ? 'selected' : ''; ?>>кожен</option>
            </select>
        </div>
        <div class="error"> <?php if(isset($errors["speciality"])) { echo 'Speciality is invalid.';}?> </div>

        <div class="full-span">
            <label for="region">Region:</label>
            <select id="region" name="region" required>
                <option value="София" <?php echo (isset($_GET['region']) && $_GET['region'] == 'София') ? 'selected' : ''; ?>>София</option>
                <option value="Бургас" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Бургас') ? 'selected' : ''; ?>>Бургас</option>
                <option value="Варна" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Варна') ? 'selected' : ''; ?>>Варна</option>
                <option value="Пловдив" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Пловдив') ? 'selected' : ''; ?>>Пловдив</option>
            </select>
        </div>
        <div class="error"> <?php if(isset($errors["region"])) { echo 'Region is invalid.';}?> </div>

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