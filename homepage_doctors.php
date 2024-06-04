<?php
session_start();

if(!isset($_SESSION['username'])) {
    header('Location: homepage.php');
    exit;
} else if(isset($_SESSION['username']) && $_SESSION['role'] === "patient") {
    header('Location: homepage_patients.php');
    exit;
}

require 'DB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $location = $_POST['appointment_location'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $dataTime = $appointment_date . ' ' . $appointment_time;

    addNewAppointment($username, $location, $dataTime);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedAppoint</title>
    <link rel="stylesheet" href="css/homepage.css">
    <script src="addNote.js"></script>
</head>
<body>
<header>
    <div id="container-logo"><img src="./images/icon.png" alt="Image is unavailable"></div>
    <div class="container-buttons">
            <a href="logout.php"> <button class="user-buttons">Излизане</button> </a>
    </div>
</header>

<main>
    <table class="results-table" id="reserved">
        <thead>
        <tr>
            <th>Резервирани часове:</th>
        </tr>
        <tr>
            <th>Пациент</th>
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

        $appointments = getReservedAppointmentsPerDoctor($_SESSION['username']);

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row["review"] . "</td>";
                if (!isset($row["rating"])) {
                    echo "<td></td>";
                } else {
                    echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                }
                if (isset($row["notes"])) {
                    echo "<td>" . $row["notes"] . "</td>";
                } else {
                    echo '<td id="note-container-' . $row['id'] . '" >
                        <form class="note-form" onsubmit="addNote(event, ' . $row['id'] . ')">
                            <input type="text" class="note-input" id="note-input-' . $row['id'] . '" required>
                            <button type="submit" class="note-button">Добави бележка</button>
                        </form>
                      </td>';
                }
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

    <div class="container-appointments">

    <table class="results-table" id="free">
        <thead>
        <tr>
            <th>Свободни часове:</th>
        </tr>
        <tr>
            <th>Локация</th>
            <th>Ден и час</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'DB.php';

        $appointments = getFreeAppointmentsPerDoctor($_SESSION['username']);

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
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

    <div class="container-form">
        <form class="appointment-form" action="./homepage_doctors.php" method="POST">

            <h2>Добави час за преглед</h2>

            <div class="form-group">
                <label for="appointment_date">Дата:</label>
                <input type="date" id="appointment_date" min="2024-06-04" name="appointment_date" required><br>
            </div>

            <div class="form-group">
                <label for="appointment_time">Час:</label>
                <input type="time" id="appointment_time" name="appointment_time" required><br>
            </div>

            <div class="form-group">
                <label for="appointment_location">Локация:</label>
                <input type="text" id="appointment_location" name="appointment_location" required><br>
            </div>

            <div class="form-group">
                <input type="submit" value="Добави часа">
            </div>
        </form>
    </div>

    </div>
</main>
</body>
</html>