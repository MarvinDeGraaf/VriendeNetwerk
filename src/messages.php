<?php
require_once('database.php');

class Messages extends Database {
    public function sendMessage($senderId, $receiverId, $message) {
        if ($senderId != '' && $receiverId != '' && $message != '') {
            $query = "INSERT INTO chats (sender_customerid, reciever_customerid, message) VALUES (?, ?, ?);";
            $params = [$senderId, $receiverId, $message];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }
    
        public function getMessages($userId, $friendId) {
            if ($userId != '' && $friendId != '') {
                $query = "SELECT * FROM chats WHERE (sender_customerid = ? AND reciever_customerid = ?) OR (sender_customerid = ? AND reciever_customerid = ?) ORDER BY timeOfSending asc;";
                $params = [$userId, $friendId, $friendId, $userId];
                return parent::voerQueryUit($query, $params);
            }
            return 0;
        }
    
        public function deleteMessage($messageId) {
            if ($messageId != '') {
                $query = "DELETE FROM chats WHERE chatid = ?;";
                $params = [$messageId];
                return parent::voerQueryUit($query, $params);
            }
            return 0;
        }

        public function deleteAllMessages($userId, $friendId) {
            if ($userId != '' && $friendId != '') {
                $query = "DELETE FROM chats WHERE (sender_customerid = ? AND reciever_customerid = ?) OR (sender_customerid = ? AND reciever_customerid = ?);";
                $params = [$userId, $friendId, $friendId, $userId];
                return parent::voerQueryUit($query, $params);
            }
            return 0;
        }
}