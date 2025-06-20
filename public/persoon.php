<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
require_once('../src/customer.php');
require_once('../src/courses.php');
require_once('../src/friendrequest.php');
require_once('../src/messages.php');
$user = $_SESSION['username'];
$customerid = $_GET['id'];
$customerService = new Customer();
$friendRequestService = new FriendRequest();
$courseService = new Courses();
$customer = $customerService->getCustomer($customerid);
$customer = $customer[0];
$firstName = $customer['firstName'];
$lastName = $customer['lastName'];
$username = $customer['username'];
if ($customer['prefix'] != null) {
    $prefix = $customer['prefix'];
} else {
    $prefix = null;
}
$courseName = $courseService->getCourse($customer['courseid']);
$courseName = $courseName[0]['name'];
$courseid = $customer['courseid'];
$age = $customer['age'];
$gender = $customer['gender'];
$outing = $customer['outing'];
$song = $customer['song'];
$film = $customer['film'];
$meal = $customer['meal'];
$professor = $customer['professor'];
if ($gender == 'M') {
    $gender = "Man";
} else if ($gender == 'F') {
    $gender = "Vrouw";
}

if ($username == $user) {
    header('location: profiel.php');
}

$userData = $customerService->getCustomerByUsername($user);
$userId = $userData[0]['customerid'];
$friends = $friendRequestService->getFriendLinkStatus($userId, $customerid);
if ($friends) {
    $messageService = new Messages();
    $email = $customer['email'];
    $address = $customer['address'];
    $city = $customer['city'];
    $country = $customer['country'];

    if (isset($_POST['sendMessage'])) {
        $senderId = $_POST['senderid'];
        $recieverId = $_POST['recieverid'];
        $message = $_POST['message'];
        $result = $messageService->sendMessage($senderId, $recieverId, $message);

        header('location: persoon.php?id=' . $customerid);
    }
    $messages = $messageService->getMessages($userId, $customerid);
    $styling = false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if ($customer['prefix'] != null) {
        echo $firstName . " " . $prefix . " " . $lastName;
    } else {
        echo $firstName . " " . $lastName;
    }
    ?></title>
    <link rel='stylesheet' href='../assets/css/persoon.css'>
    <link rel="stylesheet" href="../assets/css/navbar.css">
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
        <div>
            <h2><?php
            if ($customer['prefix'] != null) {
                echo $firstName . " " . $prefix . " " . $lastName;
            } else {
                echo $firstName . " " . $lastName;
            } ?></h2>
            <h4>gebruikersnaam: <?php echo $username; ?></h4>
            <h4>opleiding: <a href="opleiding.php?id=<?php echo $courseid; ?>"><?php echo $courseName; ?></a></h4>
            <h4>leeftijd: <?php echo $age; ?></h4>
            <h4>geslacht: <?php echo $gender; ?></h4>
            <?php
            if ($friends) {
                echo "<h4>email: " . $email . "</h4>";
                echo "<h4>adres: " . $address . "</h4>";
                echo "<h4>woonplaats: " . $city . "</h4>";
                echo "<h4>land: " . $country . "</h4>";
                echo "<h4>Interesses:</h4>";
                if ($outing != null) {
                    echo "<h4>favoriete uitgaansgelegenheid: " . $outing . "</h4>";
                }
                if ($song != null) {
                    echo "<h4>favoriete liedje: " . $song . "</h4>";
                }
                if ($film != null) {
                    echo "<h4>favoriete film: " . $film . "</h4>";
                }
                if ($meal != null) {
                    echo "<h4>favoriete eten: " . $meal . "</h4>";
                }
                if ($professor != null) {
                    echo "<h4>favoriete docent: " . $professor . "</h4>";
                }
            } else {
                echo "<h4>Interesses:</h4>";
                if ($outing != null) {
                    echo "<h4>favoriete uitgaansgelegenheid: " . $outing . "</h4>";
                }
                if ($song != null) {
                    echo "<h4>favoriete liedje: " . $song . "</h4>";
                }
                if ($film != null) {
                    echo "<h4>favoriete film: " . $film . "</h4>";
                }
                if ($meal != null) {
                    echo "<h4>favoriete eten: " . $meal . "</h4>";
                }
                if ($professor != null) {
                    echo "<h4>favoriete docent: " . $professor . "</h4>";
                }
                echo "<form action='' method='post'>
            <input type='hidden' name='customerid' value='" . $customerid . "'>
            <input type='hidden' name='username' value='" . $user . "'>
            <input type='submit' value='stuur vriendenverzoek' name='friendRequest'><br>
            </form>";
            }
            if (isset($_POST['friendRequest'])) {
                $senderId = $userId;
                $result = $friendRequestService->sendFriendRequest($senderId, $customerid);
                if ($result[0] > 0) {
                    echo "<p style='color:green;'>Vriendenverzoek verzonden!</p>";
                } else if ($result[0] == -1) {
                    echo "<p style='color:orange;'>Vriendenverzoek al verzonden!</p>";
                } else if ($result[0] == -2) {
                    $friendRequestService->acceptFriendRequest($result[1]);
                    echo "<p style='color:green;'>Vriendenverzoek al verzonden door de andere persoon! deze is nu geaccepteerd</p>";
                } else if ($result[0] == -3) {
                    echo "<p style='color:orange;'>Je bent geblokkeerd!</p>";
                }
                
                else {
                    echo "<p style='color:red;'>Vriendenverzoek mislukt!</p>";
                }
            }
            ?>
        </div>
        <?php
        if ($friends) {
            echo "<div class='messages'>";
            echo "<h2>Berichten</h2>";
            echo "<div class='message-list'>";
            foreach ($messages as $message) {
                echo "<div class='message ";
                if ($message['sender_customerid'] == $userId) {
                    echo "jij'>";
                } else {
                    echo "'>";
                }
                echo "<p class='message-content'>" . $message['message'] . "</p>";
                echo "<p class='message-time'>" . $message['timeOfSending'] . "</p>";
                if ($message['sender_customerid'] == $userId) {
                    echo "<a href='deleteMessage.php?id=" . $message['chatid'] . "&userid=" . $customerid . "'>Verwijder</a>";
                }
                echo "</div>";
            }
            echo "</div>";
            echo "<form action='' method='post' class='message-form'>";
            echo "<input type='hidden' name='senderid' value='" . $userId . "'>";
            echo "<input type='hidden' name='recieverid' value='" . $customerid . "'>";
            echo "<input type='text' name='message' placeholder='Typ hier je bericht'>";
            echo "<input type='submit' value='Verstuur' name='sendMessage'>";
            echo "</form>";
            if (isset($_POST['sendMessage'])) {
                if ($result > 0) {
                    echo "<p style='color:green;'>Bericht verzonden!</p>";
                } else {
                    echo "<p style='color:red;'>Bericht verzenden mislukt!</p>";
                }
            }
            echo "</div>";
        }
        ;
        ?>
    </main>
</body>

</html>