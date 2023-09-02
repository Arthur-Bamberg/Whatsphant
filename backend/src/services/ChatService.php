<?php
namespace Service;

require_once __DIR__ . '/../autoload.php';

use Util\PDOConnector;
use Model\Authentication;
use Controller\ChatController;

header('Content-Type: application/json');

class ChatService {
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

            self::$chatController = new ChatController(self::$pdo);

            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $response = self::$chatController->create();
                    break;

                case 'GET':
                    $response = self::$chatController->get();
                    break;

                case 'PUT':
                    $response = self::$chatController->update();
                    break;

                case 'DELETE':
                    $response = self::$chatController->delete();
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

ChatService::start();