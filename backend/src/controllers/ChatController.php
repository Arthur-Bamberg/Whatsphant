<?php

namespace Controller;

use Model\Chat;

class ChatController {
    private $pdo;
    private $thisIdUser;

    public function __construct($pdo, $thisIdUser) {
        $this->pdo = $pdo; 
        $this->thisIdUser = $thisIdUser;
    }

    public function create($otherIdUser) {
        $chat = new Chat($this->pdo, $this->thisIdUser);

        $chat->otherIdUser = $otherIdUser;

        return $chat->save();
    }

    public function get() {
        return Chat::getAll($this->pdo, $this->thisIdUser);
    }

    public function getMessagesFromChat($idChat) {
        $chat = new Chat($this->pdo, $this->thisIdUser);
        $chat->idChat = $idChat;

        if(empty($chat->get())) {
            throw new \Exception('Chat does not exist.', 404);
        }

        return $chat->getMessages();
    }

    public function delete($idChat) {
        $chat = new Chat($this->pdo, $this->thisIdUser);

        $chat->idChat = $idChat;

        if(empty($chat->get())) {
            throw new \Exception('Chat does not exist.', 404);
        }

        return $chat->delete();
    }
}