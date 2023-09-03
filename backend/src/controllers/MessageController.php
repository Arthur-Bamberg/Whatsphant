<?php

namespace Controller;

use Model\Message;

class MessageController {
    private $pdo;
    private $idUser;

    public function __construct($pdo, $idUser) {
        $this->pdo = $pdo; 
        $this->idUser = $idUser;
    }

    public function create($idChat, $content) {
        $message = new Message($this->pdo, $this->idUser);

        $message->idChat = $idChat;
        $message->content = $content;

        return $message->save();
    }

    public function update($idMessage, $content) {
        $message = new Message($this->pdo, $this->idUser);

        $message->idMessage = $idMessage;
        $message->content = $content;

        if(empty($message->get())) {
            throw new \Exception('Message not found.', 404);
        }

        return $message->update();
    }

    public function delete($idMessage) {
        $message = new Message($this->pdo, $this->idUser);

        $message->idMessage = $idMessage;

        $messageData = $message->get();

        if(empty($messageData)) {
            throw new \Exception('Message not found.', 404);
        }

        $message->idChat = $messageData->FK_idChat;

        return $message->delete();
    }
}