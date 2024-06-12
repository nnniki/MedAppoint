<?php
require '../data/DB.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   switch($_POST['type']) {
    case "note":
        saveNote();
        break;
    case "review":
        saveReview();
        break;
    case "rating":
        saveRating();
        break;
    default:
        echo "Invalid operation type";
   }
}

function saveNote() {
    $appointment_id = intval($_POST['appointment_id']);
    $note = $_POST['input'];

    $dataBase = new DB();

    $stmt = $dataBase->getConnection()->prepare("UPDATE appointments SET notes = :note WHERE id = :id");
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();
    echo $note;
    return $note;
}

function saveReview() {
    $appointment_id = intval($_POST['appointment_id']);
    $review = $_POST['input'];

    $dataBase = new DB();

    $stmt = $dataBase->getConnection()->prepare("UPDATE appointments SET review = :review WHERE id = :id");
    $stmt->bindParam(':review', $review);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();
    echo $review;
    return $review;
}

function saveRating() {
    $appointment_id = intval($_POST['appointment_id']);
    $rating = $_POST['input'];
    if ($rating < 1) {
        $rating = 1;
    }
    if ($rating > 5) {
        $rating = 5;
    }

    $dataBase = new DB();

    $stmt = $dataBase->getConnection()->prepare("UPDATE appointments SET rating = :rating WHERE id = :id");
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();
    echo $rating;
    return $rating;
}
?>
