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
</header>

<main>
    <form class="search-filter" action="./homepage_logged.php" method="GET">
        <div class="search-bar">
            <input type="text" placeholder="Search" name="name">
            <select class="filter-dropdown" name="speciality">
                <option value="" disabled selected>Специалност</option>
                <option value="кардиолог">кардиолог</option>
                <option value="кожен">кожен</option>
                <option value="УНГ">УНГ</option>
                <option value="очен">очен</option>
                <option value="гинеколог">гинеколог</option>
                <option value="невролог">невролог</option>
            </select>
            <select class="filter-dropdown" name="region">
                <option value="" disabled selected>Регион</option>
                <option value="София">София</option>
                <option value="Бургас">Бургас</option>
                <option value="Варна">Варна</option>
                <option value="Пловдив">Пловдив</option>
            </select>
            <button class="search-button">Search</button>
        </div>
    </form>
    </div>
    <table class="results-table">
        <thead>
        <tr>
            <th>Doctors</th>
            <th>Specialty</th>
            <th>Region</th>
            <th>Rating</th>
            <th>Reviews</th>
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
                // echo "<td><span class='stars'>" . str_repeat("★", $row["rating"]) . str_repeat("☆", 5 - $row["rating"]) . "</span></td>";
                // echo "<td><button class='reviews-button'>Open reviews (" . $row["reviews_count"] . ")</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No doctors found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</main>
</body>
</html>