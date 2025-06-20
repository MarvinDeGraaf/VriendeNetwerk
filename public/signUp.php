<?php
session_start();
if(isset($_SESSION['login']))
{
    header('location: ../public');
}

$error = null;
$result = null;

if (isset($_POST['submit'])) {
    require_once('../src/customer.php');
    $customer = new Customer();
    $username = $_POST['username'];
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $prefix = $_POST['prefix'];
    $courseid = $_POST['course'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $postalcode = $_POST['postalcode'];
    $outing = $_POST['outing'];
    $song = $_POST['song'];
    $film = $_POST['film'];
    $meal = $_POST['meal'];
    $professor = $_POST['professor'];

    if ($courseid == 'default') {
        $error = "Selecteer een opleiding.";
    } elseif ($age < 14) {
        $error = "Leeftijd moet groter zijn dan 14.";
    } elseif ($gender == 'default') {
        $error = "Selecteer een geslacht.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(nl|com)$/", $email) 
    || !preg_match("/^\d{4}\s?\D{2}$/", $postalcode)) {
        $error = "Ongeldig e-mailadres of Postcode.";
    } else {
        
        try {
            $result = $customer->NewCustomer($username, $hash, $firstName, $lastName, $prefix, $courseid, $age, $gender, $email, $address, $city, $country, $postalcode, $outing, $song, $film, $meal, $professor);
        } catch (Exception $e) {
            $error = "Fout bij registratie: " . $e->getMessage();
        }  
    }
    if ($result > 0) {
        header('location: login.php');
    } else {
        // $error = "Registratie mislukt! Probeer het opnieuw.";
    }

}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
</head>
<body>
    <form action="" method="post">
        <label for="username">gebruikersnaam: </label>
        <input type="text" name="username" id="username" required><br>
        <label for="password">wachtwoord: </label>
        <input type="password" name="password" id="password" required><br>
        <label for="firstname">voornaam: </label>
        <input type="text" name="firstname" id="firstname" required><br>
        <label for="lastname">acternaam: </label>
        <input type="text" name="lastname" id="lastname" required><br>
        <label for="prefix">tussenvoegsel: </label>
        <input type="text" name="prefix" id="prefix"
        ><br>
        <label for="course">opleiding: </label>
        <select name="course" id="course" required>
            <option value="default">--Selecteer een opleiding--</option>
            <?php
            require_once('../src/courses.php');
            $courses = new Courses();
            $rows = $courses->getAllCourses();
            foreach ($rows as $row) {
                echo "<option value=". $row['opleidingid'] . ">" . $row['name'] . "</option>";
            }
            ?>
        </select><br>
        <label for="age">leeftijd: </label>
        <input type="number" name="age" id="age" required><br>
        <label for="gender">geslacht: </label>
        <select name="gender" id="gender" required>
            <option value="default">--Selecteer</option>
            <option value="M">Man</option>
            <option value="F">Vrouw</option>
        </select><br>
        <label for="email">email: </label>
        <input type="email" name="email" id="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(nl|com)" required><br>
        <label for="address">adres: </label>
        <input type="text" name="address" id="address" required><br>
        <label for="postalcode">Postcode: </label>
        <input type="text" name="postalcode" id="postalcode" pattern="\d{4}\s?\D{2}" required><br>
        <label for="city">woonplaats: </label>
        <input type="text" name="city" id="city" required><br>
        <label for="country">land: </label>
        <input type="text" name="country" id="country" required><br>
        <h2>interesses</h2>
        <label> Favoriete Uitgaansgelegenheid: <input type="text" name="outing" required></label><br>
        <label> Favoriet liedje: <input type="text" name="song" required></label><br>
        <label> Favoriete film: <input type="text" name="film" required></label><br>
        <label> Favoriete eten: <input type="text" name="meal" required></label><br>
        <label> Favoriete Docent: <input type="text" name="professor" required></label><br>
        <input type="submit" name="submit" value="Registreren"><br>
    </form>
    <form action="" method="post">
        <input type="submit" value="inloggen" name="login"><br>
    </form>
    <?php
    if ($error) {
        echo "<p style='color:red;'>$error</p>";
    }
    if (isset($_POST['login'])) {
        header('location: login.php');
    }
    ?>
</body>
</html>