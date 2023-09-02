<?php

namespace Model;

class Message {
    private $pdo;
    private $idMessage;
    private $content;
    private $idChat;
    private $idUser;

    public function __construct($pdo, $idChat, $idUser) {
        $this->pdo = $pdo;
        $this->idChat = $idChat;
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
                idMessage,
                content,
                FK_idChat,
                FK_idUser,
                isActive
            FROM 
                message
            WHERE 
                idMessage = :idMessage",
            array(
                ':idMessage' => $this->idMessage
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

        return [
            'idMessage' => $this->idMessage,
            'isActive' => 0
        ];
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
            $this->$name = $value;
        } else {
            throw new \Exception("Property '$name' does not exist.");
        }
    }
}