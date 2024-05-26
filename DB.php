<?php 
class Db
{
    private $connection;

    public function __construct()
    {
        $dbhost = "mysql";
        $dbName = "medappoint";
        $userName = "root";
        $userPassword = "root";

        $this->connection = new PDO("mysql:host=$dbhost;dbname=$dbName", $userName, $userPassword,
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

function create_doctor($username, $password, $email, $first_name, $last_name, $ucn, $work_address, $phone_number, $speciality) {
    $dataBase = new Db();
    $sql = 'INSERT INTO doctors(username, password, email, first_name, last_name, egn, work_address, phone_number, speciality)
            VALUES (:username, :password, :email, :first_name, :last_name, :egn, :work_address, :phone_number, :speciality)';

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':egn', $ucn);
    $stmt->bindParam(':work_address', $work_address);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':speciality', $speciality);

    $stmt->execute();
}

function validate_username($username, $table) {
    $dataBase = new Db();

    $query = 'SELECT * FROM ' . $table . ' WHERE username = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$username]);

    if($result && $stmt->rowCount() === 1) {
        return true;
    }

    return false;
}

function validate_email($email, $table) {
    $dataBase = new Db();

    $query = 'SELECT * FROM ' . $table . ' WHERE email = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$email]);

    if($result && $stmt->rowCount() === 1) {
        return true;
    }

    return false;
}

function login($username, $password, $table) {
    $password = sha1($password);

    $dataBase = new Db();
    $query = 'SELECT * FROM ' . $table . ' WHERE `username` = :username and `password` = :password';

    $stmt =  $dataBase->getConnection()->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    $result = $stmt->execute();

    if($result && $stmt->rowCount() === 1) {
        return true;
    }

    return false;
}
