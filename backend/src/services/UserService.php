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

            self::$users = new User(self::$pdo);

            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if(!$tokenValidation->data->isAdmin) {
                        throw new \Exception('User is not admin.');
                    }

                    $params = ['name', 'email', 'password', 'img'];

                    extract(self::validateParams($params, true));

                    $response = self::$users->create(
                        $name,
                        $email,
                        $password,
                        $img
                    );
                    break;

                case 'GET':
                    $response = self::$users->get();
                    break;

                case 'PUT':
                    $idUser = self::getUriParameter();

                    if(!$tokenValidation->data->isAdmin && $tokenValidation->data->idUser != $idUser) {
                        throw new \Exception('User is unauthorized to do this action.', 403);
                    }

                    $params = ['password'];

                    extract(self::validateParams($params));

                    $response = self::$users->update(
                        $idUser,
                        $password
                    );
                    break;

                case 'DELETE':
                    $idUser = self::getUriParameter();

                    if(!$tokenValidation->data->isAdmin && $tokenValidation->data->idUser != $idUser) {
                        throw new \Exception('User is unauthorized to do this action.', 403);
                    }

                    $response = self::$users->delete(
                        $idUser
                    );
                    break;
            }
        } catch(\Exception $error) {
            if(!empty($error->getCode())) {
                $code = $error->getCode();
            } else {
                $code = 500;
            }

            http_response_code($code);

            $response = [
                'error' => $error->getMessage()
            ];
        } finally {
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}

UserService::start();