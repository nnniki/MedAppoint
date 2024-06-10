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
    <script src="addUserInput.js"></script>
</head>
<body>
<header>
    <div class="left">
    <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>
    <?php
    require_once 'DB.php';
    $patient = getPatientInformation($_SESSION['username']);
    echo '<div>
            <p class="user-info-1">' . $_SESSION['username'] . '</p>
            <p class="user-info-2">' . $patient["first_name"] . ' ' . $patient["last_name"] . '</p>
          </div>';
    ?>
    </div>
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
                <th>Резултати от търсенето:</th>
            </tr>
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
                echo "<td><a href='doctor_profile.php?id=". $row['id'] ."'> <button class='reviews-button'>Запази час</button> </a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Няма намерени доктори.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <table class="results-table" id="patient-appointments">
        <thead>
        <tr>
            <th>Резервирани часове:</th>
        </tr>
        <tr>
            <th>Доктор</th>
            <th>Ревю</th>
            <th>Рейтинг</th>
            <th>Бележки</th>
            <th>Локация</th>
            <th>Ден и час</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'DB.php';

        $appointments = getReservedAppointmentsPerPatient($_SESSION['username']);

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                date_default_timezone_set("Europe/Sofia");
                $currentDate = date('Y-m-d H:i:s');

                if (isset($row["review"])) {
                    echo "<td>" . $row["review"] . "</td>";
                } 
                else if ($row['appointment_date'] > $currentDate) {
                    echo "<td></td>";
                } 
                else {
                    echo '<td id="review-container-' . $row['id'] . '" >
                        <form class="review-form" onsubmit="addUserInput(event, ' . $row['id'] . ', \'review\')">
                            <input type="text" class="review-input" id="review-input-' . $row['id'] . '" required>
                            <button type="submit" class="review-button">Запази</button>
                        </form>
                      </td>';
                }

                if (isset($row["rating"])) {
                    echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                } 
                else if ($row['appointment_date'] > $currentDate) {
                    echo "<td></td>";
                }  
                else {
                    echo '<td id="rating-container-' . $row['id'] . '" >
                        <form class="rating-form" onsubmit="addUserInput(event, ' . $row['id'] . ', \'rating\')">
                            <input type="text" class="rating-input" min=1 max=5 id="rating-input-' . $row['id'] . '" required>
                            <button type="submit" class="rating-button">Запази</button>
                        </form>
                      </td>';
                }

                echo "<td>" . $row["notes"] . "</td>";
                echo "<td>" . $row["location"] . "</td>";
                echo "<td>" . $row["appointment_date"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Нямате резервирани часове.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</main>
</body>
</html>