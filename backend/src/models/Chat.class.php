<?php

namespace Model;

class Chat {
    private $pdo;
    private $idChat;
    private $thisIdUser;
    private $otherIdUser;

    public function __construct($pdo, $thisIdUser) {
        $this->pdo = $pdo;
        $this->thisIdUser = $thisIdUser;
    }

    public function save() {
        $this->pdo->executeSQL(
            "INSERT INTO chat (FK_idUser1, FK_idUser2) 
                VALUES (:thisIdUser, :otherIdUser)",
            array(
                ':thisIdUser' => $this->thisIdUser,
                ':otherIdUser' => $this->otherIdUser,
            )
        );

        $this->idChat = $this->pdo->getLastInsertId();

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
                idChat = :idChat",
            array(
                ':idChat' => $this->idChat
            )
        );

        return $this->pdo->getSingleResult();
    }

    public function delete() {
        $this->pdo->executeSQL(
            "DELETE FROM chat 
                WHERE idChat = :idChat",
            array(
                ':idChat' => $this->idChat
            )
        );
    }

    public function getMessages() {
        $this->pdo->query(
            "SELECT 
                message.idMessage,
                message.content,
                message.updatedOn,

                user.name as userName

            FROM 
                message

            INNER JOIN chat
                ON chat.idChat = message.FK_idChat

            INNER JOIN user
                ON user.idUser = message.FK_idUser

            WHERE 
                message.FK_idChat = :idChat
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