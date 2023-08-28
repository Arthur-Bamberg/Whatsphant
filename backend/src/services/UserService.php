<?php
namespace Service;

require_once __DIR__ . '/../autoload.php';

use Util\PDOConnector;
use Model\{
    Authentication,
    User
};

header('Content-Type: application/json');

class UserService {
    private static $pdo;
    private static $authentication;
    private static $users;

    public static function start() {
        try {
            self::$pdo = new PDOConnector();

            self::$authentication = new Authentication(self::$pdo);

            $tokenValidation = self::$authentication->validateToken();

            if(!$tokenValidation->valid) {
                throw new \Exception('Invalid token.');
            }

            if(!$tokenValidation->data->isAdmin) {
                throw new \Exception('User is not admin.');
            }

            self::$users = new User(self::$pdo);

            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $response = self::$users->create(
                        $_POST['name'],
                        $_POST['email'],
                        $_POST['password']
                    );
                    break;

                case 'GET':
                    $response = self::$users->get();
                    break;

                case 'PUT':
                    $response = self::$users->update(
                        $_GET['idUser'],
                        $_GET['password']
                    );
                    break;

                case 'DELETE':
                    $response = self::$users->delete(
                        $_GET['idUser']
                    );
                    break;
            }
        } catch(\Exception $error) {
            http_response_code(500);

            $response = [
                'error' => 'Internal Server Error',
                'message' => $error->getMessage()
            ];
        } finally {
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}

UserService::start();