<?php
namespace Service;

require_once __DIR__ . '/../autoload.php';

use Util\{
    ServiceTrait,
    PDOConnector
};
use Model\{
    Authentication,
    User
};

header('Content-Type: application/json');

class UserService {
    use ServiceTrait;

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
                    $params = ['name', 'email', 'password'];

                    extract(self::validateParams($params, true));

                    $response = self::$users->create(
                        $name,
                        $email,
                        $password
                    );
                    break;

                case 'GET':
                    $response = self::$users->get();
                    break;

                case 'PUT':
                    $params = ['idUser', 'password'];

                    extract(self::validateParams($params));

                    $response = self::$users->update(
                        $idUser,
                        $password
                    );
                    break;

                case 'DELETE':
                    $params = ['idUser'];

                    extract(self::validateParams($params));

                    $response = self::$users->delete(
                        $idUser
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