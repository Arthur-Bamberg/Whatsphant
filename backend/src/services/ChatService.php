<?php
namespace Service;

require_once __DIR__ . '/../autoload.php';

use Util\{
    ServiceTrait,
    PDOConnector
};
use Model\Authentication;
use Controller\ChatController;

header('Content-Type: application/json');

class ChatService {
    use ServiceTrait;

    private static $pdo;
    private static $authentication;
    private static $chatController;

    public static function start() {
        try {
            self::$pdo = new PDOConnector();

            self::$authentication = new Authentication(self::$pdo);

            $tokenValidation = self::$authentication->validateToken();

            if(!$tokenValidation->valid) {
                throw new \Exception('Invalid token.');
            }

            $thisIdUser = $tokenValidation->data->idUser;

            self::$chatController = new ChatController(self::$pdo, $thisIdUser);

            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $params = ['otherIdUser'];

                    extract(self::validateParams($params, true));

                    $response = self::$chatController->create($otherIdUser);
                    break;

                case 'GET':
                    $uriParameters = self::getUriParameter(true);

                    if(is_array($uriParameters) && end($uriParameters) == 'messages') {
                        $idChat = $uriParameters[0];

                        $response = self::$chatController->getMessagesFromChat($idChat);
                    } else {
                        $response = self::$chatController->get();
                    }
                    break;

                case 'DELETE':
                    $idChat = self::getUriParameter();

                    $response = self::$chatController->delete($idChat);
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