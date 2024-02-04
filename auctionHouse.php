<?php

include_once ("User.php");
include_once ("Database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['isLoggedIn'])) {
    die('You have to be authenticated to view this page');
}

/**
 * @var User $user
 */
$user           = unserialize($_SESSION['user']);
$userIsLoggedIn = $_SESSION['isLoggedIn'];

$imageLink = 'https://t3.ftcdn.net/jpg/06/77/21/80/360_F_677218014_VrMyeS8jY1u0kqxSZQLfiCfEpzgixztB.jpg';

$connection = new Database();
$properties = $connection->select('property');

$winningBids = $connection->select("property", [], "current_bidder = {$user->getId()}")

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commie Housing</title>
    <style>
        .property {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .property img {
            max-width: 100px;
            max-height: 150px;
        }
        .meal-details {
            display: flex;
            align-items: center;
        }
        .meal-info {
            margin-left: 15px;
        }
    </style>
</head>
<body>

<a href="http://localhost/biddingSite/reviewBids.php" class="redirect-admin">
    <input id="review-bids" type="hidden" value="<?= $user->isAdmin() ?>">
</a>

<a href="http://localhost/biddingSite/propertyRegistration.html" class="redirect-admin">
    <input id="register-property" type="hidden" value="<?= $user->isAdmin() ?>">
</a>

<h1>Welcome to Commie Housing!</h1>

<div>Your currently winning bids:
    <?php if (empty($winningBids)) : ?>
        <p>No bids so far</p>
    <?php else : ?>
        <?php foreach ($winningBids as $winningBid) : ?>
                    <div class="property-details property-<?= htmlspecialchars($winningBid['id'])?>">
                    <img src="<?= htmlspecialchars($imageLink) ?>" alt="<?= htmlspecialchars($winningBid['address']) ?>">
                    <div class="property-info">
                        <h2><?= htmlspecialchars($winningBid['address']) ?></h2>
                        <p>Property Type: <?= htmlspecialchars($winningBid['property_type']) ?></p>
                        <p>Bedrooms: <?= htmlspecialchars($winningBid['bedrooms']) ?></p>
                        <p>Bathrooms: <?= htmlspecialchars($winningBid['bathrooms']) ?></p>
                        <p>Parking: <?= htmlspecialchars($winningBid['parking']) ?></p>
                        <p>Description: <?= htmlspecialchars($winningBid['description']) ?></p>
                        <p>Starting Bid: £<?= number_format(htmlspecialchars($winningBid['starting_bid']), 2) ?></p>
                        <p>Current Bid: <?= htmlspecialchars($winningBid['current_bid']) == 0 ?
                                "No bids so far!" : "£" . htmlspecialchars($winningBid['current_bid']) ?></p>
                        <p>Bidding Ends At: <?= htmlspecialchars($winningBid['closing_at']) ?></p>
                        <p>Listing Status: <?= strtotime(str_replace("/", "-", $winningBid["closing_at"])) < time() ? "Expired" :
                                "Still up for auction"?></p>
                        <?php
                        switch (htmlspecialchars($winningBid['sale_approval'])) {
                            case 0:
                                echo "<p>Your bid has been rejected. Acknowledge by pressing the button below</p>";
                                echo '<input type="button" value="Acknowledge" onclick="removeListing(' . $winningBid['id'] . ')"/>';
                                break;

                            case 1:
                                echo "<p>Your bid has been approved. Acknowledge by pressing the button below</p>";
                                echo '<input type="button" value="Acknowledge" onclick="removeListing(' . $winningBid['id'] . ')"/>';
                                break;

                            default:
                                echo "<p>Bid yet to be reviewed</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2>The properties currently being auctioned are below</h2>

<div id="property-list">
    <?php foreach ($properties as $property): ?>
        <div class="property">
            <div class="property-details property-<?= htmlspecialchars($property['id'])?>">
                <img src="<?= htmlspecialchars($imageLink) ?>" alt="<?= htmlspecialchars($property['address']) ?>">
                <div class="property-info" id="property-<?= htmlspecialchars($property['id'])?>">
                    <h2><?= htmlspecialchars($property['address']) ?></h2>
                    <p>Property Type: <?= htmlspecialchars($property['property_type']) ?></p>
                    <p>Bedrooms: <?= htmlspecialchars($property['bedrooms']) ?></p>
                    <p>Bathrooms: <?= htmlspecialchars($property['bathrooms']) ?></p>
                    <p>Parking: <?= htmlspecialchars($property['parking']) ?></p>
                    <p>Description: <?= htmlspecialchars($property['description']) ?></p>
                    <p>Starting Bid: £<?= htmlspecialchars($property['starting_bid']) ?></p>
                    <p id="current-bid">Current Bid: <?= htmlspecialchars($property['current_bid']) == 0 ?
                            "No bids so far!" : "£" . htmlspecialchars($property['current_bid']) ?></p>
                    <p id="current-bidder">Current Bid Held By: <?= htmlspecialchars($property['current_bidder']) == NULL ?
                            "No bids so far!" : "Bidder #" . htmlspecialchars($property['current_bidder']) ?></p>
                    <p>Bidding Ends At: <?= htmlspecialchars($property['closing_at']) ?></p>
                    <p>Status: <?= strtotime(str_replace("/", "-", $property["closing_at"])) < time() ? "Expired" :
                        "Still up for auction"?></p>

                    <h2>If you would like to place a bid for this property, please submit one below!</h2>

                    <div class="form-group">
                        <label for="bid-value">Bid Value:</label>
                        <input type="number" step="0.01" id="bid-value" name="bid-value" title="Make sure it's more than the value of the current highest bid!" required>
                    </div>

                    <br>

                    <div class="form-group">
                        <!--<?php echo htmlspecialchars(json_encode($property));?> -->
                        <input type="submit" value="Place Bid" onclick='placeBid(<?php echo htmlspecialchars(json_encode($property));?>, <?php echo $user->getId() ?>)'>
                    </div>

                </div>
            </div>


        </div>
    <?php endforeach; ?>
</div>


<div id="output">

</div>
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>

    window.onload = () => {
        toggleAdminButton("Review Expired Listings", "review-bids");
        toggleAdminButton("Register New Properties", "register-property");
    }


    function placeBid(property, userId) {


        console.log(property); //logging the property

        axios.post('http://localhost/biddingSite/biddingScript.php', { //posting shit to the script
            LatestBid: document.getElementById("bid-value").value,
            Property : property,
            userId   : userId
        })
            .then(function (response) {

                console.log(response);

                if (response.data.length <= 0)
                {
                    console.log("Nothing has been bid on");
                    return;
                }
                console.log(response.data);

                printToScreen(response.data, property, userId);
            })
            .catch(function (error) {
                console.log('Error fetching data:', error);
            });
    }

    function printToScreen(data, property, userId) {

        if(data[0] == "Success! ") {
            document.getElementById("property-" + property["id"]).querySelector("#current-bid").innerHTML = "Current Bid: £" + document.getElementById("bid-value").value;
            document.getElementById("property-" + property["id"]).querySelector("#current-bidder").innerHTML = "Current Bid Held By: Bidder #" + userId;
        }

        let outputDiv = document.getElementById("output");
        outputDiv.innerHTML = "";

        for (let i = 0; i < data.length; i++) {
            let event = data[i];
            let output = document.createElement("p");
            output.textContent = event;

            outputDiv.appendChild(output);
        }
    }

    function removeListing(propertyId) {

        console.log(propertyId);

        axios.post('http://localhost/biddingSite/removeListingScript.php', {
            propertyId: propertyId


        })
            .then(function (response) {

                console.log(response);

                if (response.data.length <= 0)
                {
                    console.log("Nothing has been bid on");
                    return;
                }
                console.log(response.data);

                alterListing(response.data, propertyId);
            })
            .catch(function (error) {
                console.log('Error fetching data:', error);
            });
    }

    function alterListing (data, propertyId) {

        let output = document.getElementById("output");
        output.innerHTML = "";

        let childElement = document.createElement("p");
        childElement.textContent = data[1];

        if(data[0]) {

            alterView(propertyId);
        }

    }

    function alterView(propertyId) {
        let elementsToRemove = document.getElementsByClassName(`property-${propertyId}`);
        Array.from(elementsToRemove).forEach(element => {
            element.remove();
        });
    }

    function toggleAdminButton($buttonDisplay, buttonId) {
        let buttonIdName = document.getElementById(buttonId);

        console.log(buttonIdName.value);

        if(buttonIdName.value == 1) {
            buttonIdName.type = "button";
            buttonIdName.value = $buttonDisplay;
            return;
        }
        buttonIdName.type="hidden";
    }

</script>

