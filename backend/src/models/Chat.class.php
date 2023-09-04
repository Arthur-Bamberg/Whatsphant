<?php

namespace Model;

use Model\{
    User,
    Message
};

class Chat {
    private $pdo;
    private int $idChat;
    private int $thisIdUser;
    private int $otherIdUser;

    public function __construct($pdo, $thisIdUser) {
        $this->pdo = $pdo;
        $this->thisIdUser = $thisIdUser;
    }

    public function save() {
        if(empty(User::getById($this->pdo, $this->otherIdUser))) {
            throw new \Exception('User does not exist.', 404);
        }
        
        $chat = $this->getByUsers();

        if(!empty($chat)) {
            throw new \Exception('Chat already exists.', 409);
        }

        $this->pdo->executeSQL(
            "INSERT INTO chat (FK_idUser1, FK_idUser2) 
                VALUES (:thisIdUser, :otherIdUser)",
            array(
                ':thisIdUser' => $this->thisIdUser,
                ':otherIdUser' => $this->otherIdUser,
            )
        );

        $this->idChat = $this->pdo->getLastInsertedId();

        return [
            'idChat' => $this->idChat,
            'thisIdUser' => $this->thisIdUser,
            'otherIdUser' => $this->otherIdUser
        ];
    }

    public function get() {
        $this->pdo->query(
            "SELECT 
                idChat,
                FK_idUser1 as thisIdUser,
                FK_idUser2 as otherIdUser
            FROM 
                chat
            WHERE 
                idChat = :idChat
                AND (
                    FK_idUser1 = :thisIdUser1
                    OR FK_idUser2 = :thisIdUser2
                )",
            array(
                ':idChat' => $this->idChat,
                ':thisIdUser1' => $this->thisIdUser,
                ':thisIdUser2' => $this->thisIdUser
            )
        );

        $result = $this->pdo->getSingleResult();

        if(!empty($result)) {
            $this->otherIdUser = $result->otherIdUser;
        }

        return $result;
    }

    public static function getStatic($pdo, $idChat, $thisIdUser) {
        $pdo->query(
            "SELECT 
                idChat,
                FK_idUser1 as thisIdUser,
                FK_idUser2 as otherIdUser
            FROM 
                chat
            WHERE 
                idChat = :idChat
                AND (
                    FK_idUser1 = :thisIdUser1
                    OR FK_idUser2 = :thisIdUser2
                )",
            array(
                ':idChat' => $idChat,
                ':thisIdUser1' => $thisIdUser,
                ':thisIdUser2' => $thisIdUser
            )
        );

        return $pdo->getSingleResult();
    }

    private function getByUsers() {
        $this->pdo->query(
            "SELECT 
                idChat,
                FK_idUser1 as thisIdUser,
                FK_idUser2 as otherIdUser
            FROM 
                chat
            WHERE 
                (
                    FK_idUser1 = :thisIdUser1
                    AND FK_idUser2 = :otherIdUser1
                )
                OR (
                    FK_idUser1 = :otherIdUser2
                    AND FK_idUser2 = :thisIdUser2
                )",
            array(
                ':thisIdUser1' => $this->thisIdUser,
                ':otherIdUser1' => $this->otherIdUser,
                ':otherIdUser2' => $this->otherIdUser,
                ':thisIdUser2' => $this->thisIdUser
            )
        );

        return $this->pdo->getSingleResult();
    }

    public function delete() {
        $this->pdo->executeSQL(
            "UPDATE chat 
                SET FK_idLastMessage = NULL
                WHERE idChat = :idChat",
            array(
                ':idChat' => $this->idChat
            )
        );

        Message::deleteByChat($this->pdo, $this->idChat);

        $this->pdo->executeSQL(
            "DELETE FROM chat 
                WHERE idChat = :idChat",
            array(
                ':idChat' => $this->idChat
            )
        );

        return [
            'idChat' => $this->idChat,
            'thisIdUser' => $this->thisIdUser,
            'otherIdUser' => $this->otherIdUser,
            'deleted' => true
        ];
    }

    public function getMessages() {
        $this->pdo->query(
            "SELECT 
                message.idMessage,
                message.content,
                message.updatedOn,
                message.createdOn,
                (message.updatedOn <> message.createdOn) as edited,

                user.name as userName

            FROM 
                message

            INNER JOIN chat
                ON chat.idChat = message.FK_idChat

            INNER JOIN user
                ON user.idUser = message.FK_idUser

            WHERE 
                message.isActive = 1
                AND message.FK_idChat = :idChat

                AND (
                    chat.FK_idUser1 = :thisIdUser1
                    OR chat.FK_idUser2 = :thisIdUser2
                )

            ORDER BY message.createdOn ASC",
            [
                ':idChat' => $this->idChat,
                ':thisIdUser1' => $this->thisIdUser,
                ':thisIdUser2' => $this->thisIdUser
            ]
        );

        return $this->pdo->getResult();
    }

    public static function getAll($pdo, $thisIdUser) {
        $pdo->query(
            "SELECT *
                FROM (
                    SELECT
                        chat.idChat,
                        chat.FK_idUser1 as thisIdUser,
                        chat.FK_idUser2 as otherIdUser,

                        user.name as title,
                        user.imgPath as imgPath,

                        message.content as lastMessage,
                        message.updatedOn

                    FROM chat

                    LEFT JOIN message 
                        ON message.idMessage = chat.FK_idLastMessage

                    LEFT JOIN user 
                        ON user.idUser = chat.FK_idUser2

                    WHERE 
                        chat.FK_idUser1 = :thisIdUser1
                
                    UNION
                
                    SELECT
                        chat.idChat,
                        chat.FK_idUser2 as thisIdUser,
                        chat.FK_idUser1 as otherIdUser,
                        user.name as title,

                        message.content as lastMessage,
                        message.updatedOn

                    FROM chat

                    LEFT JOIN message 
                        ON message.idMessage = chat.FK_idLastMessage
                    
                    LEFT JOIN user 
                        ON user.idUser = chat.FK_idUser2
                    
                    WHERE 
                        chat.FK_idUser2 = :thisIdUser2

                ) AS data
            ORDER BY updatedOn DESC",
                [
                    ':thisIdUser1' => $thisIdUser,
                    ':thisIdUser2' => $thisIdUser
                ]
        );

        return $pdo->getResult();
    }

    public static function updateLastMessage($pdo, $idChat, $idLastMessage = NULL) {
        if(!empty($idLastMessage)) {
            $pdo->executeSQL(
                "UPDATE chat 
                    SET FK_idLastMessage = :idMessage
                    WHERE idChat = :idChat",
                array(
                    ':idMessage' => $idLastMessage,
                    ':idChat' => $idChat
                )
            );
        } else {
            $pdo->executeSQL(
                "UPDATE chat 
                    SET FK_idLastMessage = (
                        SELECT idMessage
                        FROM message
                        WHERE 
                            FK_idChat = :idChat
                            AND message.isActive = 1
                        ORDER BY createdOn DESC
                        LIMIT 1
                    )
                    WHERE idChat = :idChat",
                array(
                    ':idChat' => $idChat
                )
            );
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
            $this->$name = $value;
        } else {
            throw new \Exception("Property '$name' does not exist.");
        }
    }
}