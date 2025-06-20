<?php
require_once('database.php');

class FriendRequest extends Database
{
    public function sendFriendRequest($senderId, $receiverId)
    {
        if ($senderId != '' && $receiverId != '') {
            $query = "SELECT * FROM friendlink WHERE customeridSender = ? AND customeridReciever = ?;";
            $params = [$senderId, $receiverId];
            $result = parent::voerQueryUit($query, $params);
            if (count($result) == 0) {
                $query = "SELECT * FROM friendlink WHERE customeridSender = ? AND customeridReciever = ?;";
                $params = [$receiverId, $senderId];
                $result = parent::voerQueryUit($query, $params);
                if (count($result) == 0) {
                    $query = "INSERT INTO friendlink (customeridSender, customeridReciever, status) VALUES (?, ?, 'pending');";
                    $params = [$senderId, $receiverId];
                    $result = parent::voerQueryUit($query, $params);
                    return [$result]; // Request sent successfully
                } else {
                    return [-2, $result[0]['friendLinkid']]; // Request already exists in the opposite direction
                }
            } else {
                if ($result[0]['Status'] == 'blocked') {
                    return [-3, $result[0]['friendLinkid']]; // User is blocked
                } else {
                    return [-1]; // Request already exists
                }
            }
        }
        return [0];
    }

    public function acceptFriendRequest($requestId)
    {
        if ($requestId != '') {
            $query = "UPDATE friendlink SET status = 'accepted' WHERE friendLinkid = ?;";
            $params = [$requestId];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function rejectOrRevokeFriendRequest($requestId)
    {
        if ($requestId != '') {
            $query = "DELETE FROM friendlink WHERE friendLinkid = ?;";
            $params = [$requestId];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getAllFriends($userId, $limit = 0)
    {
        if ($userId != '') {
            if ($limit > 0) {
                $query =
                    "SELECT
                f.friendLinkid,
                c.customerid,
                c.username,
                c.firstName,
                c.lastName,
                c.prefix,
                c.email
            FROM friendLink f
            INNER JOIN customers c 
                ON c.customerid = 
            CASE 
                WHEN f.customeridSender = ? THEN f.customeridReciever
                WHEN f.customeridReciever = ? THEN f.customeridSender
            END
            WHERE 
                (f.customeridSender = ? OR f.customeridReciever = ?)
                AND f.Status = 'accepted' AND LIMIT = ?";

                $params = [$userId, $userId, $userId, $userId, $limit];
            } else {
                $query =
                    "SELECT
                f.friendLinkid,
                c.customerid,
                c.username,
                c.firstName,
                c.lastName,
                c.prefix,
                c.email
            FROM friendLink AS f
            INNER JOIN customer AS c 
                ON c.customerid = 
            CASE 
                WHEN f.customeridSender = ? THEN f.customeridReciever
                WHEN f.customeridReciever = ? THEN f.customeridSender
            END
            WHERE 
                (f.customeridSender = ? OR f.customeridReciever = ?)
                AND f.Status = 'accepted'";

                $params = [$userId, $userId, $userId, $userId];
            }
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getAllFriendsWithFirstOrLastName($userId, $firstOrLastName, $limit = 0)
    {
        if ($userId != '') {
            if ($limit > 0) {
                $query =
                    "SELECT
                f.friendLinkid,
                c.customerid,
                c.username,
                c.firstName,
                c.lastName,
                c.prefix,
                c.email
            FROM friendLink f
            INNER JOIN customers c 
                ON c.customerid = 
            CASE 
                WHEN f.customeridSender = ? THEN f.customeridReciever
                WHEN f.customeridReciever = ? THEN f.customeridSender
            END
            WHERE 
                (f.customeridSender = ? OR f.customeridReciever = ?)
                AND f.Status = 'accepted' AND LIMIT = ?";

                $params = [$userId, $userId, $userId, $userId, $limit];
            } else {
                $query =
                    "SELECT
                f.friendLinkid,
                c.customerid,
                c.username,
                c.firstName,
                c.lastName,
                c.prefix,
                c.email
            FROM friendLink AS f
            INNER JOIN customer AS c 
                ON c.customerid = 
            CASE 
                WHEN f.customeridSender = ? THEN f.customeridReciever
                WHEN f.customeridReciever = ? THEN f.customeridSender
            END
            WHERE 
                (f.customeridSender = ? OR f.customeridReciever = ?)
                AND f.Status = 'accepted'";

                $params = [$userId, $userId, $userId, $userId];
            }
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getAllFriendRequests($userId, $limit = 0)
    {
        if ($userId != '') {
            if ($limit > 0) {
                $query = "SELECT *
                FROM friendlink as f
                INNER JOIN customer as c
                ON f.customeridSender = c.customerid
                WHERE customeridReciever = ? AND status = 'pending' LIMIT $limit;";
                $params = [$userId];
            } else {
                $query = "SELECT *
                FROM friendlink as f
                INNER JOIN customer as c
                ON f.customeridSender = c.customerid
                WHERE customeridReciever = ? AND status = 'pending';";
                $params = [$userId];
            }
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getAllSentFriendRequests($userId, $limit = 0)
    {
        if ($userId != '') {
            if ($limit > 0) {
                $query = "SELECT * 
                FROM friendlink as f
                INNER JOIN customer as c
                ON f.customeridReciever = c.customerid
                WHERE customeridSender = ? AND status = 'pending' LIMIT ?;";
                $params = [$userId, $limit];
            } else {
                $query = "SELECT * 
                FROM friendlink as f
                INNER JOIN customer as c
                ON f.customeridReciever = c.customerid
                WHERE customeridSender = ? AND status = 'pending';";
                $params = [$userId];
            }
            return parent::voerQueryUit($query, $params);
        }
        if ($userId != '') {
            $query = "SELECT * 
            FROM friendlink as f
            INNER JOIN customer as c
            ON f.customeridReciever = c.customerid
            WHERE customeridSender = ? AND status = 'pending';";
            $params = [$userId];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getFriendLinkStatus($senderId, $receiverId)
    {
        if ($senderId != '' && $receiverId != '') {
            $query = "SELECT * FROM friendlink WHERE (customeridSender = ? AND customeridReciever = ?) OR (customeridReciever = ? AND customeridSender = ?);";
            $params = [$senderId, $receiverId, $senderId, $receiverId];
            $result = parent::voerQueryUit($query, $params);
            if ($result != null) {
                if ($result[0]['Status'] == 'accepted') {
                    return true; // Friends
                } else {
                    return false; // Not friends or request pending
                }
            } else {
                return false; // No friend link found
            }
        }
        return false; // Invalid input
    }

    public function getSenderByFriendLinkId($friendLinkid)
    {
        $query = "SELECT customeridSender
        FROM friendlink
        WHERE friendLinkid = ?";
        $params = [$friendLinkid];
        return parent::voerQueryUit($query, $params);
    }

    public function getReceiverByFriendLinkId($friendLinkid)
    {
        $query = "SELECT customeridReciever
        FROM friendlink
        WHERE friendLinkid = ?";
        $params = [$friendLinkid];
        return parent::voerQueryUit($query, $params);
    }

    public function blockUser($senderId, $receiverId)
    {
        if ($senderId != '' && $receiverId != '') {
            $query = "UPDATE friendlink SET status = 'blocked' WHERE (customeridSender = ? AND customeridReciever = ?) OR (customeridReciever = ? AND customeridSender = ?);";
            $params = [$senderId, $receiverId, $senderId, $receiverId];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }
}