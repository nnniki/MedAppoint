<?php 
class Db
{
    private $connection;

    public function __construct()
    {
     $dbhost = "localhost";
     $port = 3307;
     $dbName = "medappoint";
     $userName = "root";
     $userPassword = "";

     $this->connection = new PDO("mysql:host=$dbhost;port=$port;dbname=$dbName", $userName, $userPassword,
         [
             PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         ]);

        //   $dbhost = "mysql";
        //   $dbName = "medappoint";
        //   $userName = "root";
        //   $userPassword = "root";

        //   $this->connection = new PDO("mysql:host=$dbhost;dbname=$dbName", $userName, $userPassword,
        //       [
        //           PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        //           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //       ]);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

function create_patient($username, $password, $email, $first_name, $last_name, $ucn, $address, $phone_number) {
    $dataBase = new Db();
    $sql = 'INSERT INTO patients(username, password, email, first_name, last_name, egn, address, phone_number)
            VALUES (:username, :password, :email, :first_name, :last_name, :egn, :address, :phone_number)';

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':egn', $ucn);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone_number', $phone_number);

    $stmt->execute();
}

function create_doctor($username, $password, $email, $first_name, $last_name, $ucn, $work_address, $region, $phone_number, $speciality) {
    $dataBase = new Db();
    $sql = 'INSERT INTO doctors(username, password, email, first_name, last_name, egn, work_address, region, phone_number, speciality)
            VALUES (:username, :password, :email, :first_name, :last_name, :egn, :work_address, :region, :phone_number, :speciality)';

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':egn', $ucn);
    $stmt->bindParam(':work_address', $work_address);
    $stmt->bindParam(':region', $region);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':speciality', $speciality);

    $stmt->execute();
}

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

function login($username, $password, $table) {
    $password = sha1($password);

    $dataBase = new Db();
    $query = 'SELECT * FROM ' . $table . ' WHERE `username` = :username and `password` = :password';

    $stmt =  $dataBase->getConnection()->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    $result = $stmt->execute();

    return $result && $stmt->rowCount() === 1;
}

function getDoctorsInformation($searchName, $searchRegion, $searchSpeciality) {
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

function getDoctorInformation($id) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name, speciality, region FROM doctors WHERE id=$id";

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->execute();
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    return $doctor;
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

    return round($rating);
}

function getReservedAppointmentsPerDoctor($username) {
    $dataBase = new Db();
    $sql = "SELECT doctors.username, patients.first_name, patients.last_name, review, rating, notes, location, appointment_date, appointments.id
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    JOIN patients ON patients.id = appointments.patient_id
    WHERE doctors.username = :username AND appointment_date >= :date";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    date_default_timezone_set("Europe/Sofia");
    $currentDate = date('Y-m-d H:i:s');
    $stmt->bindParam(':date', $currentDate);

    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}

function getFreeAppointmentsPerDoctor($username) {
    $dataBase = new Db();
    $sql = "SELECT doctors.username, location, appointment_date
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    WHERE doctors.username = :username AND patient_id IS NULL AND appointment_date >= :date";

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

function validate_new_appointment($id, $dateTime) {
    $dataBase = new Db();

    $query = 'SELECT * FROM appointments WHERE doctor_id = :id AND appointment_date = :dateTime';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':dateTime', $dateTime);

    $result = $stmt->execute();

    return $result && $stmt->rowCount() >= 1;
}

function getFreeAppointmentsPerID($id) {
    $dataBase = new Db();
    $sql = "SELECT first_name, last_name, review, rating, notes, location, appointment_date
    FROM doctors JOIN appointments ON doctors.id = appointments.doctor_id
    WHERE doctor_id=$id AND patient_id IS NULL AND appointment_date >= :date";

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
            WHERE doctor_id = :id AND (review IS NOT NULL OR rating IS NOT NULL)";

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
    WHERE patients.username = :username";

    $stmt =  $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
  
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $appointments;
}