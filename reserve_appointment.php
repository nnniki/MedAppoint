<?php
require 'DB.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];

    $dataBase = new DB();

    $username = $_POST['patient_username'];
    $sql = "UPDATE appointments
            SET patient_id = (SELECT id
                              FROM patients
                              WHERE username = :username)
            WHERE id = :id";

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();

    $text = "Успешна резервация!";
    echo $text;
    return $text;
}