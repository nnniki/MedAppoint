<?php

require '../../data/DB.php';
require 'validations.php';

function getDoctorInformationPerUsernameForHeader($username) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name FROM doctors WHERE username = :username";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    return $patient;
}

function getReservedAppointmentsPerDoctor($username) {
    $dataBase = new Db();
    $sql = "SELECT doctors.username, patients.first_name, patients.last_name, review, rating, notes, location, appointment_date, appointments.id
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    JOIN patients ON patients.id = appointments.patient_id
    WHERE doctors.username = :username AND appointment_date >= :date
    ORDER BY appointment_date DESC";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    date_default_timezone_set("Europe/Sofia");
    $currentDate = date('Y-m-d H:i:s');
    $stmt->bindParam(':date', $currentDate);

    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}

function saveNote($appointment_id, $note) {
    $dataBase = new DB();

    $stmt = $dataBase->getConnection()->prepare("UPDATE appointments SET notes = :note WHERE id = :id");
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':id', $appointment_id);

    $stmt->execute();

}

function getFreeAppointmentsPerDoctor($username) {
    $dataBase = new Db();
    $sql = "SELECT doctors.username, location, appointment_date, appointments.id
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    WHERE doctors.username = :username AND patient_id IS NULL AND appointment_date >= :date
    ORDER BY appointment_date DESC";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    date_default_timezone_set("Europe/Sofia");
    $currentDate = date('Y-m-d H:i:s');
    $stmt->bindParam(':date', $currentDate);

    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}

function addNewAppointment($username, $location, $dateTime) {
    $dataBase = new Db();
    $doctor_id = "SELECT id FROM doctors WHERE username = :username";

    $stmt = $dataBase->getConnection()->prepare($doctor_id);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $doctor_id = $result['id'];

    if (validate_new_appointment($doctor_id, $dateTime)) {
        return false;
    }

    $sql = 'INSERT INTO appointments(doctor_id, location, appointment_date)
            VALUES (:doctor_id, :location, :dateTime)';

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':dateTime', $dateTime);

    $stmt->execute();

    return true;
}