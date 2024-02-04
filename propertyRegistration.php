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

$address      = htmlspecialchars($_POST['address']);
$propertyType = htmlspecialchars($_POST['property-type']);
$bedrooms     = htmlspecialchars($_POST['bedrooms']);
$bathrooms    = htmlspecialchars($_POST['bathrooms']);
$parking      = htmlspecialchars($_POST['parking']);
$description  = htmlspecialchars($_POST['description']);
$startingBid  = htmlspecialchars($_POST['starting-bid']);
$closingDate  = htmlspecialchars($_POST['closing-date']);
$closingTime  = htmlspecialchars($_POST['closing-time']);

$closingAt = trim($closingDate) . " " . trim($closingTime);

$propertyData = [
    'address'       => $address,
    'property-type' => $propertyType,
    'bedrooms'      => $bedrooms,
    'bathrooms'     => $bathrooms,
    'parking'       => $parking,
    'description'   => $description,
    'starting-bid'  => $startingBid,
    'closing-at'    => $closingAt
];

$isValidPropertyData = Property::validatePropertyData($propertyData);

if (!$isValidPropertyData) {
    die('Invalid property data, cannot register this property.');
}

$property   = new Property($address, $propertyType, $bedrooms, $bathrooms, $parking, $description, $startingBid,
    $closingAt);

$property->register($connection);

#endofscript