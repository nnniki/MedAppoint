<?php
session_start();

if(isset($_SESSION['username']) && $_SESSION['role'] === "patient") {
   
    header('Location: homepage_patients.php');

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
            <a href="logout.php"> <button class="user-buttons">Logout</button> </a>
    </div>
</header>

<main>
<table class="results-table">
        <thead>
        <tr>
            <th>Patients</th>
            <th>Rating</th>
            <th>Reviews</th>
            <th>Notes</th>
            <th>Location</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'DB.php';

        $appointments = getAppointmentsPerDoctor($_SESSION['username']);

        if (count($appointments) > 0) {
            foreach ($appointments as $row) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                echo "<td>" . $row["review"] . "</td>";
                echo "<td>" . $row["notes"] . "</td>";
                echo "<td>" . $row["location"] . "</td>";
                echo "<td>" . $row["appointment_date"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No appointments found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</main>

</body>
</html>