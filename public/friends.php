<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
require_once('../src/customer.php');
require_once('../src/friendrequest.php');
$username = $_SESSION['username'];
$customerService = new Customer();
$friendRequestService = new FriendRequest();
$result = $customerService->getCustomerByUsername($username);
$userid = $result[0]['customerid'];
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $friends = $friendRequestService->getAllFriendsWithFirstOrLastName($userid, $search);
} else {
    $friends = $friendRequestService->getAllFriends($userid);
}
$aantal = count($friends);

foreach ($friends as $friend) {
    if ($friend['username'] == $username) {
        $aantal--;
    }
}

if ($aantal == 0) {
    $result = "we hebben geen vrienden gevonden maar je kan ze toevoegen";
} else {
    if ($aantal == 1) {
        $result = "we hebben 1 vriend gevonden";
    } else {
        $result = "we hebben $aantal vrienden gevonden";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>Vrienden</title>
</head>

<body>
    <div>
        <nav>
            <img src="../assets/Images/VriendeNetwerk.png" alt="LogoVriendenNetwerk">
            <form action="" method="post">
                <input type="text" name="search" id="search" placeholder="Zoek vrienden.">
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
        <div>
            <div>
                <h2><?php echo $result; ?></h2>
                <table>
                    <?php
                    foreach ($friends as $friend) {
                        if ($friend['username'] != $username) {
                            echo "<tr>";
                            echo "<td><a href='persoon.php?id=" . $friend['customerid'] . "'>" . $friend['username'] . ' (' . $friend['firstName'] . ' ' . $friend['prefix'] . ' ' . $friend['lastName'] . ")</a> <a href=wijzigVriendschap.php?id=" . $friend['friendLinkid'] . "&action=delete><button>Verwijder vriend</button></a></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
            </div>
            <div>
                <a href="inkomendeVriendschapVerzoeken.php"><button>Inkomende vriendschapverzoeken</button></a>
                <a href="uitgaandeVerzoeken.php"><button>Uitgaande vriendschapverzoeken</button></a>
            </div>
        </div>
    </main>
</body>

</html>