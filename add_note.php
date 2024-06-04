<?php
require 'DB.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $note = $_POST['note'];

    $dataBase = new DB();

    $stmt = $dataBase->getConnection()->prepare("UPDATE appointments SET notes = :note WHERE id = :id");
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();
    return $note;
}
?>
