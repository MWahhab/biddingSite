<?php

class AuctionHouseController
{
    private Database $connection;
    private User     $user;

    /**
     * @param Database $connection
     * @param User $user
     */
    public function __construct(Database $connection, User $user)
    {
        $this->connection = $connection;
        $this->user       = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param array $property
     * @param float $latestBid
     * @return void               Initiates purchase and completes all validation checks.
     *
     *                            Also updates the book's quantity in the database as well as the customer's balance
     */
    public function initiateBid(array $property, float $latestBid):void
    {
        $this->user->setEvents([]);
        $closingTimestamp = strtotime(str_replace("/", "-", $property["closing_at"]));
        $currentTime = time();

        if (empty($property)) {
            $this->user->addEvent("No property was bid on. \n");
            return;
        }

        if($property["current_bid"] >= $latestBid || $property["starting_bid"] >= $latestBid) {
            $this->user->addEvent("You didn't bid higher than the existing bid. Your bid has failed");
            return;
        }

        if($property["current_bidder"] == $this->getUser()->getId()) {
            $this->user->addEvent("You already hold the latest bid for this property! You can't up your own bid!");
            return;
        }

        if($closingTimestamp < $currentTime) {
            $this->user->addEvent("Sorry! The deadline for bidding on his property has passed! 
            This can no longer be bid on");
            return;
        }

        $this->user->addEvent("Success! ");
        $this->connection->update("property", ["current_bid" => $latestBid], ["id" => $property["id"]]);
        $this->connection->update("property", ["current_bidder" => $this->user->getId()], ["id" => $property["id"]]);
        $this->user->addEvent("Your bid has been processed. Please refresh the page to view your winning bids list");
        
    }

    public function reviewResult(int $propertyId, bool $decision): void
    {

        if($decision) {
            $this->connection->update("property", ["sale_approval" => 1], ["id" => $propertyId]);
            return;
        }

        $this->connection->update("property", ["sale_approval" => 0], ["id" => $propertyId]);
    }

public function removeListing(int $id): array
    {
        $property = $this->connection->select("property", [], "id = {$id}", 1);

        if (empty($property)) {
            return [false, "Unfortunately, this listing doesn't exist and cant be removed."];
        }

        $address = $property['address'];

        $this->connection->delete("property", ["id" => $id]);
        return [true, "The property located at {$address} has been successfully removed."];

    }

}

