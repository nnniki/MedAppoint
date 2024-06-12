<?php

require '../../data/DB.php';

function getPatientInformationForHeader($username) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name FROM patients WHERE username = :username";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    return $patient;
}

function getSearchedDoctorsInformation($searchName, $searchRegion, $searchSpeciality) {
    $dataBase = new Db();
    $sql = "SELECT id, first_name, last_name, speciality, region FROM doctors WHERE 1=1";

    $params = [];
    if (!empty($searchName)) {
        $sql .= " AND CONCAT(first_name,' ',last_name) LIKE ?";
        $keyword = '%' . $searchName . '%';
        $params[] = $keyword;
    }

    if (!empty($searchSpeciality)) {
        $sql .= " AND speciality = ?";
        $params[] = $searchSpeciality;
    }

    if (!empty($searchRegion)) {
        $sql .= " AND region = ?";
        $params[] = $searchRegion;
    }

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->execute($params);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $doctors;
}

function getAverageRatingPerDoctor($id) {
    $dataBase = new Db();
    $sql = "SELECT AVG(appointments.rating) AS rating
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    WHERE doctor_id = :id";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $rating = $result['rating'];

    if ($rating === NULL) {
        $rating = 1;
    }

    return round($rating);
}

function getDoctorInformationForCardPerID($id) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name, speciality, work_address, region FROM doctors WHERE id=:id";

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':id', $id);

    $stmt->execute();
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    return $doctor;
}

function getFreeAppointmentsPerIdForDoctorProfile($id) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name, review, rating, notes, location, appointment_date, appointments.id
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    WHERE doctor_id=$id AND patient_id IS NULL AND appointment_date >= :date
    ORDER BY appointment_date DESC";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    date_default_timezone_set("Europe/Sofia");
    $currentDate = date('Y-m-d H:i:s');
    $stmt->bindParam(':date', $currentDate);

    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}

function getReviewsPerId($id) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name, review, rating
            FROM appointments JOIN patients ON patient_id = patients.id
            WHERE doctor_id = :id AND (review IS NOT NULL OR rating IS NOT NULL)
            ORDER BY appointment_date DESC";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':id', $id);

    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $reviews;
}

function getReservedAppointmentsPerPatient($username) {
    $dataBase = new Db();
    $sql = "SELECT doctors.first_name, doctors.last_name, review, rating, notes, location, appointment_date, appointments.id
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    JOIN patients ON patients.id = appointments.patient_id
    WHERE patients.username = :username
    ORDER BY appointment_date DESC";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}