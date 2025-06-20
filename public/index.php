<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
$username = $_SESSION['username'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>VriendeNetwerk</title>
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
        <div class="welkom">
            <h1>Welkom bij VriendeNetwerk</h1>
            <p>Hallo, <?php echo $username; ?>!</p>
            <form action="" method="post">
                <input type="submit" value="Uitloggen" name="logout"><br>
                <?php
                if (isset($_POST['logout'])) {
                    session_destroy();
                    header('location: login.php');
                }
                ?>
            </form>
        </div>
        <div class="vrienden">
            <table>
                <tr>
                    <th colspan="2">Vrienden</th>
                </tr>
                <?php
                require_once('../src/friendrequest.php');
                require_once('../src/customer.php');
                $customerService = new Customer();
                $friendRequestService = new FriendRequest();
                $result = $customerService->getCustomerByUsername($username);
                $userid = $result[0]['customerid'];
                $friends = $friendRequestService->getAllFriends($userid);

                foreach ($friends as $friend) {
                    if ($friend['username'] != $username) {
                        if ($friend['prefix'] != null) {
                            echo "<tr>";
                            echo "<td><a href='persoon.php?id=" . $friend['customerid'] . "'>" . $friend['username'] . ' (' . $friend['firstName'] . ' ' . $friend['prefix'] . ' ' . $friend['lastName'] . ")</a></td>";
                            echo "</tr>";
                        } else {
                            echo "<tr>";
                            echo "<td><a href='persoon.php?id=" . $friend['customerid'] . "'>" . $friend['username'] . ' (' . $friend['firstName'] . ' ' . $friend['lastName'] . ")</a></td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td><a href='index.php?id=" . $friend['customerid'] . "'>Bekijk Chats</a></td>";
                    }
                }
                ?>
            </table>
            <table>
                Inkomende Verzoeken
                <tr>
                    <th>Naam</th>
                    <th>Status</th>
                    <th>Accepteren</th>
                    <th>Afwijzen</th>
                </tr>
                <?php
                $incomingRequests = $friendRequestService->getAllFriendRequests($userid, 10);
                foreach ($incomingRequests as $request) {
                    echo "<tr>";
                    echo "<td>" . $request['username'] . "</td>";
                    echo "<td>" . $request['Status'] . "</td>";
                    echo "<td><a href='wijzigVriendschap.php?id=" . $request['friendLinkid'] . "&action=accept'>Accepteren</a></td>";
                    echo "<td><a href='wijzigVriendschap.php?id=" . $request['friendLinkid'] . "&action=reject'>Afwijzen</a></td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <table>
                Uitgaande Verzoeken
                <tr>
                    <th>Naam</th>
                    <th>Status</th>
                    <th>Herroepen</th>
                </tr>
                <?php
                $outgoingRequests = $friendRequestService->getAllSentFriendRequests($userid);
                foreach ($outgoingRequests as $request) {
                    echo "<tr>";
                    if ($request['prefix'] != null) {
                        echo "<td>" . $request['firstName'] . ' ' . $request['prefix'] . ' ' . $request['lastName'] . ' (' . $request['username'] . ")</td>";
                    } else {
                        echo "<td>" . $request['firstName'] . ' ' . $request['lastName'] . ' (' . $request['username'] . ")</td>";
                    }
                    echo "<td>" . $request['Status'] . "</td>";
                    echo "<td><a href='wijzigVriendschap.php?id=" . $request['friendLinkid'] . "&action=revoke'>Herroepen</a></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <?php if (isset($_GET['id'])): ?>
            <div class='messages';>
                <h2>Berichten</h2>
                <div class='message-list'>
                <?php
                if (isset($_GET['id'])) {
                    $friendId = $_GET['id'];
                    require_once('../src/messages.php');
                    $messageService = new Messages();
                    $messages = $messageService->getMessages($userid, $friendId);
                    if (empty($messages)) {
                        echo "<p>Geen berichten gevonden.</p>";
                    }
                    foreach ($messages as $message) {
                        echo "<div class='message ";
                        if ($message['sender_customerid'] == $friendId) {
                            echo "jij'>";
                        } else {
                            echo "'>";
                        }
                        echo "<p class='message-content'>" . $message['message'] . "</p>";
                        echo "<p class='message-time'>" . $message['timeOfSending'] . "</p></div>";
                    }
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>