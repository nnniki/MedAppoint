<?php
session_start();

if(!isset($_SESSION['username'])) {
    header('Location: homepage.php');
    exit;
} else if(isset($_SESSION['username']) && $_SESSION['role'] === "patient") {
    header('Location: homepage_patients.php');
    exit;
}

require_once '../../business/DoctorController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $location = $_POST['appointment_location'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $dateTime = $appointment_date . ' ' . $appointment_time . ':00';

    if (!addNewAppointment($username, $location, $dateTime)) {
        $error = 'Вече имате добавен преглед в този час и дата.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedAppoint</title>
    <link rel="stylesheet" href="../css/homepage.css">
    <script src="../js/add_user_input.js"></script>
    <script src="../js/cancel_appointment.js"></script>
</head>
<body>
<header>
    <div class="left">
        <div id="container-logo"><img src="../images/icon.png" alt="Image is unavailable"></div>
        <?php
        require_once '../../business/DoctorController.php';
        $patient = getDoctorInformationPerUsernameForHeader($_SESSION['username']);
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
    <table class="results-table" id="reserved">
        <thead>
        <tr>
            <th>Резервирани часове:</th>
        </tr>
        <tr>
            <th>Пациент</th>
            <th>Локация</th>
            <th>Ден и час</th>
            <th>Бележки</th>
            <th>Ревю</th>
            <th>Рейтинг</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once '../../business/DoctorController.php';

        $appointments = getReservedAppointmentsPerDoctor($_SESSION['username']);

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
                date_default_timezone_set("Europe/Sofia");
                $current_date = date('Y-m-d H:i:s');

                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row["location"] . "</td>";
                echo "<td>" . $row["appointment_date"] . "</td>";

                if (isset($row["notes"])) {
                    echo "<td>" . $row["notes"] . "</td>";
                } else {
                    echo '<td id="note-container-' . $row['id'] . '" >
                        <form class="note-form" onsubmit="addUserInput(event, ' . $row['id'] . ', \'note\')">
                            <input type="text" class="note-input" id="note-input-' . $row['id'] . '" required>
                            <button type="submit" class="note-button">Запази</button>
                        </form>
                      </td>';
                }

                echo "<td>" . $row["review"] . "</td>";
                if (!isset($row["rating"])) {
                    echo "<td></td>";
                } else {
                    echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                }
                
                if ($row['appointment_date'] > $current_date) {
                    echo '<td class="reserve-button" id="cancel-appointment-container-' . $row['id'] . '" >
                    <button class="reviews-button" onclick="cancelAppointment(event, ' . $row['id'] . ', \'delete\')">Откажи час</button>
                    </td>';
                }

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Няма намерени часове.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="container-appointments">

        <div class="container-form">
            <form class="appointment-form" action="./homepage_doctors.php" method="POST">

                <h2>Добави час за преглед</h2>

                <div class="form-group">
                    <label for="appointment_date">Дата:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                    <script>
                        const today = new Date().toISOString().split('T')[0];
                        document.getElementById('appointment_date').setAttribute('min', today);
                    </script>
                </div>

                <div class="form-group">
                    <label for="appointment_time">Час:</label>
                    <input type="time" id="appointment_time" min="07:00" max="21:00" name="appointment_time" required>
                </div>

                <div class="form-group">
                    <label for="appointment_location">Локация:</label>
                    <input type="text" id="appointment_location" name="appointment_location" required>
                </div>

                <div class="form-group">
                    <input type="submit" value="Добави часа">
                </div>

                <div class="error"> <?php if(isset($error)) { echo $error;}?> </div>
            </form>
        </div>

        <table class="results-table" id="free-doctor">
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
            require_once '../../business/DoctorController.php';

            $appointments = getFreeAppointmentsPerDoctor($_SESSION['username']);

            if (count($appointments) > 0) {
                foreach ($appointments as $row) {
                    echo "<tr>";
                    echo "<td>" . $row["location"] . "</td>";
                    echo "<td>" . $row["appointment_date"] . "</td>";
                    echo '<td class="cancel-button" id="cancel-appointment-container-' . $row['id'] . '" >
                             <button class="reviews-button" onclick="cancelAppointment(event, ' . $row['id'] . ', \'delete\')">Откажи час</button>
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