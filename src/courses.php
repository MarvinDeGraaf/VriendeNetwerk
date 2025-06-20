<?php
require_once('database.php');

class Courses extends Database {
    public function getAllCourses() {
        $query = "SELECT opleidingid, name FROM opleiding";
        return parent::voerQueryUit($query);
    }

    public function insertNewCourse($name, $description, $address, $city, $country) {
        if ($name != '' && $description != '' && $address != '' && $city != '' && $country != '') {
            $query = "INSERT INTO opleiding (name, description, address, city, country) VALUES (?, ?, ?, ?, ?);";
            $params = [$name, $description, $address, $city, $country];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }

    public function getCourse($id) {
        $query = "SELECT * FROM opleiding WHERE opleidingid = ?;";
        $params = [$id];
        return parent::voerQueryUit($query, $params);
    }
}