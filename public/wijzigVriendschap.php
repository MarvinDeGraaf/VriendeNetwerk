<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../public/login.php');
}
require_once('../src/friendrequest.php');
require_once('../src/messages.php');
$friendRequestService = new FriendRequest();
$messageService = new Messages();
switch ($_GET['action'])
{
    case 'revoke':
        DeleteFriend();
        header('location: index.php');
        break;
    case 'reject':
        BlockFriend();
        header('location: index.php');
        break;
    case 'accept':
        $friendRequestService->acceptFriendRequest($_GET['id']);
        header('location: index.php');
        break;
    case 'delete':
        DeleteFriend();
        header('location: friends.php');
    default:
        echo 'error';
        break;
}

function DeleteFriend() {
    $friendRequestService = new FriendRequest();
    $messageService = new Messages();   
    $friend1 = $friendRequestService->getSenderByFriendLinkId($_GET['id'])[0]['customeridSender'];
    $friend2 = $friendRequestService->getReceiverByFriendLinkId($_GET['id'])[0]['customeridReciever'];
    $friendRequestService->rejectOrRevokeFriendRequest($_GET['id']);
    $messageService->deleteAllMessages($friend1, $friend2);
}

function BlockFriend() {
    $friendRequestService = new FriendRequest();
    $messageService = new Messages();   
    $friend1 = $friendRequestService->getSenderByFriendLinkId($_GET['id'])[0]['customeridSender'];
    $friend2 = $friendRequestService->getReceiverByFriendLinkId($_GET['id'])[0]['customeridReciever'];
    $friendRequestService->blockUser($friend1, $friend2);
    $messageService->deleteAllMessages($friend1, $friend2);
}