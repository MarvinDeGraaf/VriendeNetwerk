<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: index.php');
}
require_once('../src/courses.php');
require_once('../src/customer.php');

$courseService = new Courses();
$customerService = new Customer();

$courseid = $_GET['id'];
$course = $courseService->getCourse($courseid)[0];

$courseName = $course['name'];
$courseDesc = $course['description'];
$courseAddress = $course['address'];
$courseCity = $course['city'];
$courseCountry = $course['country'];

$students = $customerService->getCustomersByCourse($courseid);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>Info Opleiding <?php echo $courseName; ?></title>
</head>
<body>
    <div>
        <nav>
            <img src="../assets/Images/VriendeNetwerk.png" alt="LogoVriendenNetwerk">
            <form action="vriendenzoek.php" method="post">
                <input type="text" name="search" id="search" placeholder="Zoek Nieuwe vrienden.">
                <input type="submit" value="Zoeken" name="searchBtn"><br>
            </form>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profiel.php">Profiel</a></li>
                <li><a href="friends.php">Vrienden</a></li>
            </ul>
        </nav>
    </div>
    <h1><?php echo $courseName; ?></h1>
    <h4><?php echo $courseDesc; ?></h4>
    <h2>Locatie</h2>
    <p><?php echo $courseAddress . ", " . $courseCity . ", " . $courseCountry; ?></p>
    <h2>Aantal Ingeschreven Studenten: <?php echo count($students); ?></h2>
    <h2>Totaal Aantal Studenten op de site: <?= count($customerService->getAllCustomers()); ?></h2>
    <h2>Ingeschreven Studenten</h2>
    <ul>
        <?php foreach ($students as $student): ?>
            <li><?php echo $student['firstName'] . ' ' . $student['prefix'] . ' ' . $student['lastName']; ?> (<?php echo $student['username']; ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>