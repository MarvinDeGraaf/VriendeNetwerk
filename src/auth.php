<?php
require_once('database.php');

Class Authenticate extends Database {
    public function checkLogin($username, $password) {
        $query = "SELECT * FROM customer WHERE username = ?";
        $params = [$username];
        $result = parent::voerQueryUit($query, $params);
        
        if ($result && password_verify($password, $result[0]['password'])) {
            return true;
        }
        return false;
    }
}