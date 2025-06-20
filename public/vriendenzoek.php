<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
require_once('../src/customer.php');
$_POST['search'] = isset($_POST['search']) ? $_POST['search'] : null;
$search = $_POST['search'];
$username = $_SESSION['username'];
$customerService = new Customer();
$customers = $customerService->getCustomersByName($search);
$aantal = count($customers);
foreach ($customers as $customer) {
    if ($customer['username'] == $username) {
        $aantal--;
    }
}

if ($aantal == 0) {
    $customers = $customerService->getCustomersByOpleiding($search);
    $aantal = count($customers);
    foreach ($customers as $customer) {
        if ($customer['username'] == $username) {
            $aantal--;
        }
    }
}

if ($aantal == 0) {
    $result = "we hebben geen mensen gevonden";
} else {
    if ($aantal == 1) {
        $result = "we hebben 1 persoon gevonden";
    } else {
        $result = "we hebben $aantal personen gevonden";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/vriendenzoek.css">
    <title>VriendeNetwerk Zoeken</title>
</head>

<body>
    <div>
        <nav>
            <img src="../assets/Images/VriendeNetwerk.png" alt="LogoVriendenNetwerk">
            <form action="" method="post">
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

    <main>
        <div class="results">
            <h2><?php echo $result; ?></h2>

            <?php if (isset($customers)) {
                foreach ($customers as $customer) {
                    if ($customer['username'] != $username) {
                        echo "<div class='customer-card'><a href='persoon.php?id=" . $customer['customerid'] . "'>";
                        echo "<h3>" . $customer['firstName'] . " " . $customer['prefix'] . " " . $customer['lastName'] . " (" . $customer['username'] . ")</h3></a>";
                        echo "</div>";
                    }
                }
            } ?>
        </div>
    </main>
</body>

</html>