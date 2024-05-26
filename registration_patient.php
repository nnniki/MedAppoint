<?php

require_once 'DB.php';
// session_start();

// if(isset($_SESSION['email'])) {
//     header('Location: home.php');
// }

function validate_registration_form(string $username, string $email, string $password, string $first_name, string $last_name, string $ucn, string $address, string $phone_number)
{
    // global $connection;
    // include_once '../mysql/database.php';

    $errors = [];

    $validate_username = validate_patient_username($username);

    if($validate_username->rowCount() >= 1) {
        $errors['username'] = 'This username is invalid, please choose another one!';
    }

    $validate_email = validate_patient_email($email);

    if($validate_email->rowCount() >= 1) {
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

        $_SESSION['email'] = $email;
        //header('Location: login_patients.php');
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
            <input type="submit" value="Регистрация">
        </div>

    </div>
    </form>

</body>
</html>