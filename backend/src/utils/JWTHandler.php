<?php
namespace Util;

use Model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private $secretKey;
    private $algorithm;
    private $user;

    public function __construct($integrationPDO, $secretKey, $algorithm = 'HS256') {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
        $this->user = new User($integrationPDO);
    }

    public function generateJWT($idUser, $isAdmin) {
        $issuedAt = time();
        $expirationTime = $issuedAt + constant('JWT_EXPIRATION_MINUTES') * 60;

        $token = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => array(
                'iss' => $_SERVER['HTTP_HOST'],
                'idUser' => $idUser,
                'isAdmin' => $isAdmin,
                'iat' => $issuedAt            
            )
        );

        $this->user->updateTokenData($idUser, date('Y-m-d H:i:s', $issuedAt), date('Y-m-d H:i:s', $expirationTime));

        return JWT::encode($token, $this->secretKey, $this->algorithm);
    }

    public function validateJWT($token) {
        $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));

        if($decoded->data->iss != $_SERVER['HTTP_HOST']) {
            throw new \Exception('Invalid token issuer.');
        }

        $result = $this->user->validateTokenData(
            $decoded->data->idUser, 
            $decoded->data->isAdmin, 
            date('Y-m-d H:i:s', $decoded->data->iat), 
            date('Y-m-d H:i:s', $decoded->exp)
        );

        return (object) array('valid' => true, 'data' => $result);
    }
}