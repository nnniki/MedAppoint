<?php
require '../data/DB.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $type = $_POST['type'];

    $text = "";
    if ($type === "delete") {
        $text = delete_appointment($appointment_id);
    } else if ($type === "update") {
        $text = update_appointment($appointment_id);
    } else {
        $text = "Invalid operation";
    }

    return $text;
}

function delete_appointment($appointment_id) {
    $dataBase = new Db();
    $sql = "DELETE FROM appointments
            WHERE id = :id";

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();

    $text = "Успешно премахната резервация";
    echo $text;
    return $text;
}

function update_appointment($appointment_id) {
    $dataBase = new Db();
    $sql = "UPDATE appointments
            SET patient_id = NULL, notes = NULL
            WHERE id = :id";

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();

    $text = "Успешно премахната резервация";
    echo $text;
    return $text;
}