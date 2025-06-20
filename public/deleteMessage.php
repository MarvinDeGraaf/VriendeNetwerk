<?php
require_once('../src/messages.php');
$messageService = new Messages();
$messageService->deleteMessage($_GET['id']);
header("location: ../public/persoon.php?id=" . $_GET['userid']);