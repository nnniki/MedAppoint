<?php

function validate_username($username, $table) {
    $dataBase = new Db();

    $query = 'SELECT * FROM ' . $table . ' WHERE username = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$username]);

    return $result && $stmt->rowCount() === 1;
}

function validate_email($email, $table) {
    $dataBase = new Db();

    $query = 'SELECT * FROM ' . $table . ' WHERE email = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$email]);

    return $result && $stmt->rowCount() === 1;
}

function validate_new_appointment($id, $dateTime) {
    $dataBase = new Db();

    $query = 'SELECT * FROM appointments WHERE doctor_id = :id AND appointment_date = :dateTime';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':dateTime', $dateTime);

    $result = $stmt->execute();

    return $result && $stmt->rowCount() >= 1;
}