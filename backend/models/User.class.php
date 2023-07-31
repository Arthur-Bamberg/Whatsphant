<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $email, $password) {
        if(empty($name)) {
            throw new Exception("Empty name.");
        }

        if(!$this->isValidEmail($email)) {
            throw new Exception("Invalid email address.");
        }

        if(!$this->isValidPassword($password)) {
            throw new Exception("Invalid password.");
        }

        $this->pdo->executeSQL(
            "INSERT INTO user (name, email, password) 
                VALUES (:name, :email, :password)",
            array(
                ':name' => $name,
                ':email' => $email,
                ':password' => md5(sha1($password))
            )
        );

        return [
            'idUser' => $this->pdo->getLastInsertId(),
            'name' => $name,
            'email' => $email
        ];
    }

    public function get() {
        $this->pdo->query(
            "SELECT 
                idUser, 
                name, 
                email, 
                isActive 
            FROM user
            ORDER BY name ASC"
        );

        return $this->pdo->getResultArray();
    }

    public function update($idUser, $password) {
        if(!$this->isValidPassword($password)) {
            throw new Exception("Invalid password.");
        }

        $this->pdo->executeSQL(
            "UPDATE user 
                SET user.password = :password
            WHERE idUser = :idUser",
            array(
                ':idUser' => $idUser,
                ':password' => md5(sha1($password))
            )
        );

        return [
            'idUser' => $idUser
        ];
    }

    public function delete($idUser) {
        $this->pdo->executeSQL(
            "UPDATE user 
                SET user.isActive = 0
            WHERE idUser = :idUser",
            array(
                ':idUser' => $idUser
            )
        );

        return [
            'idUser' => $idUser,
            'isActive' => 0
        ];
    }

    public function validateLogin($email, $password) {
        if(!$this->isValidEmail($email)) {
            throw new Exception("Invalid email address.");
        }

        if(!$this->isValidPassword($password)) {
            throw new Exception("Invalid password.");
        }

        $this->pdo->query(
            "SELECT 
                idUser,
                isAdmin 
            FROM user
            WHERE 
                user.isActive = 1 AND
                user.email = :email AND
                user.password = :password",
            array(
                ':email' => $email,
                ':password' => md5(sha1($password))
            )
        );

        $result = $this->pdo->getResult();

        if($result) {
            return (object) $this->pdo->getResult();
        } else {
            throw new Exception('Invalid email or password.');
        }
    }

    public function updateTokenData($idUser, $issuedAt, $expirationTime) {
        $this->pdo->executeSQL(
            "UPDATE user 
                SET user.issuedAt = :issuedAt,
                    user.expirationTime = :expirationTime
            WHERE idUser = :idUser",
            array(
                ':idUser' => $idUser,
                ':issuedAt' => $issuedAt,
                ':expirationTime' => $expirationTime
            )
        );
    }

    public function validateTokenData($idUser, $isAdmin, $issuedAt, $expirationTime) {
        $this->pdo->query(
            "SELECT 
                idUser,
                isAdmin
            FROM user
            WHERE 
                user.isActive = 1 AND
                user.idUser = :idUser AND
                user.isAdmin = :isAdmin AND
                user.issuedAt = :issuedAt AND
                user.expirationTime = :expirationTime",
            array(
                ':idUser' => $idUser,
                ':isAdmin' => $isAdmin,
                ':issuedAt' => $issuedAt,
                ':expirationTime' => $expirationTime,
            )
        );

        $result = $this->pdo->getResult();

        if($result) {
            return (object) $this->pdo->getResult();
        } else {
            throw new Exception('Invalid token data.');
        }
    }

    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    function isValidPassword($password) {
        return (strlen($password) >= 8) &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password);
    }
}