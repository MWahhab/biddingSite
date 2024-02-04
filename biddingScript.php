<?php
include_once ("Database.php");
include_once("AuctionHouseController.php");
include_once ("User.php");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$json        = file_get_contents('php://input');
$requestData = json_decode($json, true);

$bidValue = (float) $requestData['LatestBid'];
$property = (array) $requestData['Property'];
$userId   = (int)   $requestData['userId'];


if (!$userId || empty($property)) {
    return;
}

try {
    $connection = new Database();
} catch (Exception $e)  {
    echo $e->getMessage();
    return;
}
/**
 * @var User $user
 */
$user = unserialize($_SESSION['user']);
$temp = $user->getId();
$controller = new AuctionHouseController($connection, $user);
$controller->initiateBid($property, $bidValue);

$_SESSION['user'] = serialize($user);

$events = $user->getEvents();



$json   = json_encode($events);
echo $json;

