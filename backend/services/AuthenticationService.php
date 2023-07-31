<?php
require_once __DIR__ . '/../../services/PDOConnector.php';
require_once __DIR__ . '/../model/Authentication.class.php';

header('Content-Type: application/json');

class AuthenticationService {
    private static $pdo;
    private static $authentication;

    public static function start() {
        try {
            self::$pdo = new PDOConnector();
            self::$authentication = new Authentication(self::$pdo);

            $response = self::$authentication->authenticate();
        } catch (Exception $error) {
            http_response_code(401);

            $response = [
                'error' => 'Unauthorized',
                'message' => $error->getMessage()
            ];
        } finally {
            echo json_encode($response);
        }
    }
}

AuthenticationService::start();