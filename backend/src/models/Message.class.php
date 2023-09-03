<?php

namespace Model;

use Model\Chat;
class Message {
    private $pdo;
    private int $idMessage;
    private $content;
    private int $idChat;
    private int $idUser;

    public function __construct($pdo, $idUser) {
        $this->pdo = $pdo;
        $this->idUser = $idUser;
    }

    public function save() {
        $this->pdo->executeSQL(
            "INSERT INTO message (content, FK_idChat, FK_idUser) 
                VALUES (:content, :idChat, :idUser)",
            array(
                ':content' => $this->content,
                ':idChat' => $this->idChat,
                ':idUser' => $this->idUser
            )
        );

        $this->idMessage = $this->pdo->getLastInsertedId();

        Chat::updateLastMessage($this->pdo, $this->idChat, $this->idMessage);

        return [
            'idMessage' => $this->idMessage,
            'content' => $this->content,
            'idChat' => $this->idChat,
            'idUser' => $this->idUser
        ];
    }

    public function get() {
        $this->pdo->query(
            "SELECT 
                message.idMessage,
                message.content,
                message.FK_idChat,
                message.FK_idUser,
                message.isActive
            FROM 
                message

                INNER JOIN chat 
                    ON message.FK_idChat = chat.idChat

                INNER JOIN user
                    ON chat.FK_idUser1 = user.idUser OR chat.FK_idUser2 = user.idUser

            WHERE 
                message.idMessage = :idMessage
                AND message.isActive = 1
                AND user.idUser = :idUser",
            array(
                ':idMessage' => $this->idMessage,
                ':idUser' => $this->idUser
            )
        );

        return $this->pdo->getSingleResult();
    }

    public function update() {
        $this->pdo->executeSQL(
            "UPDATE message 
                SET content = :content
                WHERE idMessage = :idMessage",
            array(
                ':content' => $this->content,
                ':idMessage' => $this->idMessage
            )
        );

        return [
            'idMessage' => $this->idMessage,
            'content' => $this->content
        ];
    }

    public function delete() {
        $this->pdo->executeSQL(
            "UPDATE message 
                SET isActive = 0
                WHERE idMessage = :idMessage",
            array(
                ':idMessage' => $this->idMessage
            )
        );

        Chat::updateLastMessage($this->pdo, $this->idChat);

        return [
            'idMessage' => $this->idMessage,
            'isActive' => 0
        ];
    }

    public static function deleteByChat($pdo, $idChat) {
        $pdo->executeSQL(
            "DELETE FROM message
                WHERE FK_idChat = :idChat",
            array(
                ':idChat' => $idChat
            )
        );
    }

    private function validateIdChat() {
        if(empty(Chat::getStatic($this->pdo, $this->idChat, $this->idUser))) {
            throw new \Exception('Chat not found.', 404);
        }
    }

    public function __get($name) {
        if(array_key_exists($name, get_class_vars(get_class($this)))) {
            return $this->$name;
        } else {
            throw new \Exception("Property '$name' does not exist.");
        }
    }

    public function __set($name, $value) {
        if(array_key_exists($name, get_class_vars(get_class($this)))) {
            if($name == 'idChat') {
                $this->idChat = $value;

                $this->validateIdChat();
            } else {
                $this->$name = $value;
            }
        } else {
            throw new \Exception("Property '$name' does not exist.");
        }
    }
}