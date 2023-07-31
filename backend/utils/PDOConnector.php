<?php
require_once 'globals.php';

class PDOConnector {
    private $pdo;
    private $stmt;

    public function __construct() {
        $host = constant('DB_HOST');
        $database = constant('DB_NAME');
        $username = constant('DB_USER');
        $password = constant('DB_PASS');

        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Error connecting to database: " . $e->getMessage());
        }
    }    

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->stmt = $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error executing query: " . $e->getMessage());
        }
    }

    public function getLastInsertedId() {
        return (int) $this->pdo->lastInsertId();
    }

    public function getModelResult($class) {
        return $this->stmt->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function getResult() {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }
}