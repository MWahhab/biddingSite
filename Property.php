<?php

class Property
{
    private int    $id;
    private string $address;
    private string $propertyType;
    private int    $bedrooms;
    private int    $bathrooms;
    private string $parking;
    private string $description;
    private float  $startingBid;
    private float  $currentBid;
    private int    $currentBidder;
    private string $closingAt;

    /**
     * @param string $address
     * @param string $propertyType
     * @param int    $bedrooms
     * @param int    $bathrooms
     * @param string $parking
     * @param string $description
     * @param float  $startingBid
     * @param string $closingAt
     */
    public function __construct(string $address, string $propertyType, int $bedrooms, int $bathrooms, string $parking,
    string $description, float $startingBid, string $closingAt)
    {
        $this->address      = $address;
        $this->propertyType = $propertyType;
        $this->bedrooms     = $bedrooms;
        $this->bathrooms    = $bathrooms;
        $this->parking      = $parking;
        $this->description  = $description;
        $this->startingBid  = $startingBid;
        $this->closingAt    = $closingAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): void
    {
        $this->propertyType = $propertyType;
    }

    public function getBedrooms(): int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): void
    {
        $this->bedrooms = $bedrooms;
    }

    public function getBathrooms(): int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(int $bathrooms): void
    {
        $this->bathrooms = $bathrooms;
    }

    public function getParking(): string
    {
        return $this->parking;
    }

    public function setParking(string $parking): void
    {
        $this->parking = $parking;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStartingBid(): float
    {
        return $this->startingBid;
    }

    public function setStartingBid(float $startingBid): void
    {
        $this->startingBid = $startingBid;
    }

    public function getCurrentBid(): float
    {
        return $this->currentBid;
    }

    public function setCurrentBid(float $currentBid): void
    {
        $this->currentBid = $currentBid;
    }

    public function getCurrentBidder(): int
    {
        return $this->currentBidder;
    }

    public function setCurrentBidder(int $currentBidder): void
    {
        $this->currentBidder = $currentBidder;
    }

    public function getClosingAt(): string
    {
        return $this->closingAt;
    }

    public function setClosingAt(string $closingAt): void
    {
        $this->closingAt = $closingAt;
    }



    public function register(Database $connection): void
    {
        if (!empty($connection->select("property", [], "address = '{$this->getAddress()}'"))) {
            die('Property is already on auction.');
        }

        $propertyData = [
            'address'        => $this->getAddress(),
            'property_type'  => $this->getPropertyType(),
            'bedrooms'       => $this->getBedrooms(),
            'bathrooms'      => $this->getBathrooms(),
            'parking'        => $this->getParking(),
            'description'    => $this->getDescription(),
            'starting_bid'   => $this->getStartingBid(),
            'closing_at'     => $this->getClosingAt(),
            'current_bidder' => NULL
        ];

        $connection->insert('property', $propertyData);
        header("Location: http://localhost/biddingSite/auctionHouse.php");
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function validatePropertyData(array $data): bool
    {
        if(!isset($data['address'], $data['property-type'], $data['bedrooms'], $data['bathrooms'], $data['parking'],
            $data['description'], $data['starting-bid'], $data['closing-at'])) {
            return false;
        }

        return true;
    }


}