<?php
include_once ("Database.php");
include_once("AuctionHouseController.php");
include_once ("User.php");

$json        = file_get_contents('php://input');
$requestData = json_decode($json, true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = unserialize($_SESSION['user']);

try {
    $connection = new Database();
} catch (Exception $e)  {
    echo $e->getMessage();
    return;
}


$propertyId = (int) $requestData['propertyId'];

if(!$propertyId) {
    return;
}

$controller = new AuctionHouseController($connection, $user);

$json = json_encode($controller->removeListing($propertyId));

echo $json;