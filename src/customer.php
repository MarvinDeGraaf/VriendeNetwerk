<?php
require_once('database.php');

class Customer extends Database
{
    public function NewCustomer($username, $hash, $firstName, $lastName, $prefix, $courseid, $age, $gender, $email, $address, $city, $country, $postalcode, $outing, $song, $film, $meal, $professor)
    {
        if ($username != '' && $hash != '' && $firstName != '' && $lastName != '' && $courseid != "default" && $age > 0 && $address != '' && $city != '' && $country != '' && $postalcode != '' && $outing != '' && $song != '' && $film != '' && $meal != '' && $professor != '') {
            if ($prefix != '') {
                $query = "INSERT INTO `customer`(`username`, `password`, `firstName`, `lastName`, `prefix`, `courseid`, `age`, `gender`, `email`, `address`, `city`, `country`, `postalcode`, `outing`, `song`, `film`, `meal`, `professor`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";//12
                $params = [$username, $hash, $firstName, $lastName, $prefix, $courseid, $age, $gender, $email, $address, $city, $country, $postalcode, $outing, $song, $film, $meal, $professor];
            } else {
                $query = "INSERT INTO `customer`(`username`, `password`, `firstName`, `lastName`, `courseid`, `age`, `gender`, `email`, `address`, `city`, `country`, `postalcode`, `outing`, `song`, `film`, `meal`, `professor`) 
                        VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
                $params = [$username, $hash, $firstName, $lastName, $courseid, $age, $gender, $email, $address, $city, $country, $postalcode, $outing, $song, $film, $meal, $professor];

            }
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }


    public function getCustomer($id)
    {
        $query = "SELECT * FROM customer WHERE customerid = ?;";
        $params = [$id];
        return parent::voerQueryUit($query, $params);
    }

    public function getCustomersByName($name)
    {
        $query = "SELECT * FROM customer 
        WHERE CONCAT(firstName, lastName) LIKE ? 
        OR CONCAT(firstName, prefix, lastName) LIKE ?
        LIMIT 200;";
        $params = ["%$name%", "%$name%"];
        return parent::voerQueryUit($query, $params);
    }

    public function getCustomersByOpleiding($opleiding)
    {
        $query = "SELECT * FROM customer AS c
        INNER JOIN opleiding AS o ON c.courseid = o.opleidingid
        WHERE o.name LIKE ? LIMIT 200;";
        $params = ["%$opleiding%"];
        return parent::voerQueryUit($query, $params);
    }

    public function getCustomerByUsername($username)
    {
        $query = "SELECT * FROM customer WHERE username LIKE ? LIMIT 1;";
        $params = [$username];
        return parent::voerQueryUit($query, $params);
    }

    public function getAllCustomers()
    {
        $query = "SELECT * FROM customer;";
        return parent::voerQueryUit($query, []);
    }


    public function updateCustomerImage($customerid, $imageId)
    {
        $query = "UPDATE customer
        SET imageId=?
        WHERE customerid = ?";
        $values = [$imageId, $customerid];

        parent::voerQueryUit($query, $values);
    }

    public function getCustomerImage($customerid)
    {
        $query = "SELECT imageId
        FROM customer
        WHERE customerid = ?";

        return parent::voerQueryUit($query, [$customerid]);
    }

    public function getCustomersByCourse($courseid)
    {
        $query = "SELECT * FROM customer WHERE courseid = ?;";
        $params = [$courseid];
        return parent::voerQueryUit($query, $params);
    }

    public function updateCustomer($customerid, $courseid, $age, $gender, $email)
    {
        $query = "UPDATE customer SET courseid = ?, age = ?, gender = ?, email = ? WHERE customerid = ?;";
        $params = [$courseid, $age, $gender, $email, $customerid];
        return parent::voerQueryUit($query, $params);
    }

    public function updateCustomerHobbies($outing, $song, $film, $meal, $professor, $customerid)
    {
        if ($outing != '' && $song != '' && $film != '' && $meal != '' && $professor != '') {
            $query = "UPDATE customer SET outing = ?, song = ?, film = ?, meal = ?, professor = ? WHERE customerid = ?;";
            $params = [$outing, $song, $film, $meal, $professor, $customerid];
            return parent::voerQueryUit($query, $params);
        }
        return 0;
    }
}
