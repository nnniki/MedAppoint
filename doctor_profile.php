<?php
session_start();

if(isset($_SESSION['username']) && $_SESSION['role'] === "doctor") {
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
    <script src="reserve_appointment.js"></script>
</head>
<body>
<header>
    <div class="left">
        <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>
        <?php
        if(isset($_SESSION['username'])) {
            require_once 'DB.php';
            $patient = getPatientInformation($_SESSION['username']);
            echo '<div>
                    <p class="user-info-1">' . $_SESSION['username'] . '</p>
                    <p class="user-info-2">' . $patient["first_name"] . ' ' . $patient["last_name"] . '</p>
                  </div>';
        }
        ?>
    </div>
    <?php
        if(isset($_SESSION['username'])) {
            echo "<div class='container-buttons'>
                <a href='homepage_patients.php'> <button class='user-buttons'>Назад</button> </a>
                <a href='logout.php'> <button class='user-buttons'>Излизане</button> </a>
                </div>";
        }
        else {
//            echo "<div class='container-buttons'>
//            <a href='registration_patient.php'> <button class='user-buttons'>Регистрация за пациент</button> </a>
//            <a href='login_patients.php'><button class='user-buttons'>Вход за пациент</button></a>";
            echo "<div class='container-buttons'>
            <a href='homepage.php'> <button class='user-buttons'>Назад</button> </a>";
        }

    ?>

</header>

<main>
    <div class="container-appointments">

        <div class="doctor-information">
            <div class="doctor-card">
                <h2>Информация за доктора</h2>
                <div class="doctor-info">
                    <?php
                    require_once 'DB.php';
                    $doctor = getDoctorInformation($_GET['id']);

                    echo "<div class='info-item'>
                            <label>Име:</label>
                            <span id='doctor-name'>" . $doctor["first_name"] . " " . $doctor["last_name"] . "</span>
                        </div>";
                    echo "<div class='info-item'>
                            <label>Специалност:</label>
                            <span id='doctor-specialty'>" . $doctor["speciality"] . "</span>
                        </div>";
                    echo "<div class='info-item'>
                            <label>Работен адрес:</label>
                            <span id='doctor-address'>" . $doctor["work_address"] . "</span>
                    </div>";
                    echo "<div class='info-item'>
                            <label>Регион:</label>
                            <span id='doctor-region'>" . $doctor["region"] . "</span>
                    </div>";
                    $doctors_rating = getAverageRatingPerDoctor($_GET['id']);
                    echo "<div class='info-item'><label>Рейтинг:</label>";
                    if (!isset($doctors_rating)) {
                        echo "</div>";
                    } else {
                        echo "<span class='stars' id='doctor-stars'>" . str_repeat("★", $doctors_rating) . str_repeat("☆", 5 - $doctors_rating) . "</span></div>";
                    }
                    ?>
                </div>
            </div>

            <table class="results-table" id="reviews">
                <thead>
                <tr>
                    <th>Отзиви:</th>
                </tr>
                <tr>
                    <th>Пациент</th>
                    <th>Ревю</th>
                    <th>Оценка</th>
                </tr>
                </thead>
                <tbody>
                <?php
                require_once 'DB.php';

                $reviews = getReviewsPerId($_GET['id']);

                if (count($reviews) > 0) {
                    foreach ($reviews as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                        echo "<td>" . $row["review"] . "</td>";
                        if (!isset($row["rating"])) {
                            echo "<td></td>";
                        } else {
                            echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Няма отизиви за дадения специалист.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <table class="results-table" id="free">
            <thead>
                <tr>
                    <th>Свободни часове:</th>
                </tr>
                <tr>
                    <th>Доктор</th>
                    <th>Локация</th>
                    <th>Дата и час</th>
                </tr>
            </thead>
            <tbody>
            <?php
            require_once 'DB.php';

            $appointments = getFreeAppointmentsPerId($_GET['id']);
            $logged = 0;
            $username = "";
            if (isset($_SESSION['username'])) {
                $logged = 1;
                $username = $_SESSION['username'];
            }

            if (count($appointments) > 0) {
                foreach ($appointments as $row) {
                    echo "<tr>";
                    echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                    echo "<td>" . $row["location"] . "</td>";
                    echo "<td>" . $row["appointment_date"] . "</td>";
                    echo '<td class="reserve-button" id="reserve-appointment-container-' . $row['id'] . '" >
                             <button class="reviews-button" onclick="reserveAppointment(event, ' . $logged . ', ' . $row['id'] . ' , \'' . $username . '\')">Запази час</button>
                      </td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Няма намерени часове.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>