<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
$username = $_SESSION['username'];
require_once('../src/customer.php');
require_once('../src/friendrequest.php');
$customerService = new Customer();
$friendRequestService = new FriendRequest();

$userid = $customerService->getCustomerByUsername($username)[0]['customerid'];
$AllUitgoingfriendRequests = $friendRequestService->getAllFriendRequests($userid);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/friendsAndRequests.css">
    <title>Inkomende Vezoeken van <?php echo $username; ?></title>
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
    <main>
        <div class="vriendInfo">
            <table>
                <tr>
                    <th>naam</th>
                    <th>status</th>
                    <th>accepteren</th>
                    <th>afwijzen</th>
                </tr>
                <?php
                foreach ($AllUitgoingfriendRequests as $friendrequest) {
                    echo "<tr>";
                    if ($friendrequest['prefix'] != null) {
                        echo "<td>" . $friendrequest['firstName'] . ' ' . $friendrequest['prefix'] . ' ' . $friendrequest['lastName'] . ' (' . $friendrequest['username'] . ")</td>";
                    } else {
                        echo "<td>" . $friendrequest['firstName'] . $friendrequest['lastName'] . ' (' . $friendrequest['username'] . ")</td>";
                    }
                    echo "<td>" . $friendrequest['Status'] . "</td>";
                    echo "<td><a href='wijzigVriendschap.php?id=" . $friendrequest['friendLinkid'] . "&action=accept'>accepteer</a></td>";
                    echo "<td><a href='wijzigVriendschap.php?id=" . $friendrequest['friendLinkid'] . "&action=reject'>wijs af</a></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </main>
</body>

</html>