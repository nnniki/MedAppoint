<?php
session_start();

if(!isset($_SESSION['username'])) {
    header('Location: homepage.php');
    exit;
} else if(isset($_SESSION['username']) && $_SESSION['role'] === "doctor") {
    header('Location: homepage_doctors.php');
     exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedAppoint</title>
    <link rel="stylesheet" href="css/homepage.css">
</head>
<body>
<header>
    <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>
    <div class="container-buttons">
            <a href="logout.php"> <button class="user-buttons">Излизане</button> </a>
    </div>
</header>

<main>
    <form action="./homepage_patients.php" method="GET">
        <div class="search-bar">
            <input type="text" placeholder="Търсене" name="name">
            <select name="speciality">
                <option value="" disabled selected>Специалност</option>
                <option value="кардиолог">кардиолог</option>
                <option value="кожен">кожен</option>
                <option value="УНГ">УНГ</option>
                <option value="очен">очен</option>
                <option value="гинеколог">гинеколог</option>
                <option value="невролог">невролог</option>
            </select>
            <select name="region">
                <option value="" disabled selected>Регион</option>
                <option value="София">София</option>
                <option value="Бургас">Бургас</option>
                <option value="Варна">Варна</option>
                <option value="Пловдив">Пловдив</option>
            </select>
            <button class="search-button">Търсене</button>
        </div>
    </form>
    
    <table class="results-table" id="results">
        <thead>
        <tr>
            <th>Доктор</th>
            <th>Специалност</th>
            <th>Регион</th>
            <th>Рейтинг</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'DB.php';
        $searchName = "";
        $searchRegion = "";
        $searchSpeciality = "";

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['name'])) {
                $searchName = $_GET['name'];
            }
            if (isset($_GET['speciality'])) {
                $searchSpeciality = $_GET['speciality'];
            }
            if (isset($_GET['region'])) {
                $searchRegion = $_GET['region'];
            }
        }
        $doctors = getDoctorsInformation($searchName, $searchRegion, $searchSpeciality);

        if (count($doctors) > 0) {
            foreach ($doctors as $row) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row["speciality"] . "</td>";
                echo "<td>" . $row["region"] . "</td>";
                $doctors_rating = getAverageRatingPerDoctor($row['id']);
                if (!isset($doctors_rating)) {
                    echo "<td></td>";
                } else {
                    echo "<td><span class='stars'>" . str_repeat("★", $doctors_rating) . str_repeat("☆", 5 - $doctors_rating) . "</span></td>";
                }
                echo "<td><button class='reviews-button'>Запази час</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Няма намерени доктори.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <table class="results-table">
        <thead>
        <tr>
            <th>Доктор</th>
            <th>Локация</th>
            <th>Дата и час</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'DB.php';

        $appointments = getFreeAppointments();

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row["location"] . "</td>";
                echo "<td>" . $row["appointment_date"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Няма намерени часове.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</main>
</body>
</html>