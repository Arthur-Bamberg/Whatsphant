<?php
namespace Service;

require_once __DIR__ . '/../autoload.php';

use Util\{
    ServiceTrait,
    PDOConnector
};
use Model\Authentication;
use Controller\MessageController;

header("Access-Control-Allow-Origin: http://localhost:5000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

class ChatService {
    use ServiceTrait;

    private static $pdo;
    private static $authentication;
    private static $messageController;

    public static function start() {
        try {
            self::$pdo = new PDOConnector();

            self::$authentication = new Authentication(self::$pdo);

            $tokenValidation = self::$authentication->validateToken();

            if(!$tokenValidation->valid) {
                throw new \Exception('Invalid token.');
            }

            $thisIdUser = $tokenValidation->data->idUser;

            self::$messageController = new MessageController(self::$pdo, $thisIdUser);

            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $params = ['idChat', 'content'];

                    extract(self::validateParams($params, true));

                    $response = self::$messageController->create($idChat, $content);
                    break;

                case 'PUT':
                    $idMessage = self::getUriParameter();

                    $params = ['content'];

                    extract(self::validateParams($params));

                    $response = self::$messageController->update($idMessage, $content);

                    break;

                case 'DELETE':
                    $idMessage = self::getUriParameter();

                    $response = self::$messageController->delete($idMessage);
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

ChatService::start();