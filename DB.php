<?php 
class Db
{
    private $connection;

    public function __construct()
    {
        $dbhost = "localhost";
        $dbName = "medappoint";
        $userName = "root";
        $userPassword = "";

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
    $sql = 'INSERT INTO patients(username, password, email, first_name, last_name, ucn, address, phone_number)
            VALUES (:username, :password, :email, :first_name, :last_name, :ucn, :address, :phone_number)';

    $stmt = $dataBase->getConnection()->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':ucn', $ucn);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone_number', $phone_number);

    $stmt->execute();
}

function validate_patient_username($username) {
    $dataBase = new Db();

    $query = 'SELECT * FROM users WHERE username = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$username]);

    return $result;
}

function validate_patient_email($email) {
    $dataBase = new Db();

    $query = 'SELECT * FROM users WHERE email = ?';
    $stmt =  $dataBase->getConnection()->prepare($query);
    $result = $stmt->execute([$email]);

    return $result;
}

