<?php
namespace Model;

use Util\JWTHandler;
use Model\User;

class Authentication {
    private $JWTHandler;
    private $user;

    public function __construct($pdo) {
        $this->JWTHandler = new JWTHandler($pdo, constant("JWT_SECRET_KEY"));
        $this->user = new User($pdo);
    }

    public function authenticate() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $loginData = $this->user->validateLogin($email, $password);

        $token = $this->JWTHandler->generateJWT($loginData->idUser, $loginData->isAdmin);
        
        return [
            'token' => $token
        ];
    }

    public function validateToken() {
        $authHeader = getallheaders()['Authorization'] ?? null;
        if (!empty($authHeader)) {
            $token = null;

            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
            }

            if ($token) {
                return $this->JWTHandler->validateJWT($token);
            } else {
                throw new \Exception("Token not found in request.");
            }
        } else {
            throw new \Exception("Authorization header not present in the request.");
        }
    }
}