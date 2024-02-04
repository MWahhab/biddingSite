<?php
require_once ('Database.php');
require_once ('Property.php');

try {
    $connection = new Database();
} catch (Exception $e) {
    die($e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!$_SESSION["isAdmin"]) {
    die("You ain't no admin lil man");
}

/**
 * @var User $user
 */
$user = unserialize($_SESSION['user']);

$id           = htmlspecialchars($_POST['id']);
$startingBid  = htmlspecialchars($_POST['starting-bid']);
$closingDate  = htmlspecialchars($_POST['closing-date']);
$closingTime  = htmlspecialchars($_POST['closing-time']);

$closingAt = trim($closingDate) . " " . trim($closingTime);

$propertyData = [
    'id'            => $id,
    'starting-bid'  => $startingBid,
    'closing-at'    => $closingAt
];

if(!isset($propertyData['starting-bid']) || !isset($propertyData['closing-at']) || !isset($propertyData['id'])) {
    die('Invalid relist data, cannot register this property.');
}

$connection->update("property", ["starting_bid" => $propertyData["starting-bid"],
    "closing_at" => $propertyData["closing-at"]], ["id" => $propertyData["id"]]);
