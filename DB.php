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
    $sql = "SELECT first_name, last_name, speciality, region FROM doctors WHERE 1=1";

    $params = [];
    if (!empty($searchName)) {
        $sql .= " AND CONCAT(first_name,' ',last_name) LIKE ?";
        $keyword = '%' . $searchName . '%';

        $params[] = $keyword;
    }

    if (!empty($searchSpeciality)) {
        $sql .= " AND speciality LIKE ?";
        $keyword = '%' . $searchSpeciality . '%';
        $params[] = $keyword;
    }

    if (!empty($searchRegion)) {
        $sql .= " AND region LIKE ?";
        $keyword = '%' . $searchRegion . '%';
        $params[] = $keyword;
    } 
    
    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->execute($params);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);


    return $doctors;
}