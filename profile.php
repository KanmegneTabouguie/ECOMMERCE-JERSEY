<?php
session_start();

// Include your database connection file
include 'database.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Query to retrieve user information
    $queryUser = "SELECT * FROM user WHERE user_id = ?";
    $stmtUser = mysqli_prepare($conn, $queryUser);
    mysqli_stmt_bind_param($stmtUser, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmtUser);
    $resultUser = mysqli_stmt_get_result($stmtUser);

    if ($resultUser) {
        // Check if there is a matching user
        if ($rowUser = mysqli_fetch_assoc($resultUser)) {
            // User information found

            // Extract user information
            $username = $rowUser['username'];
            $email = $rowUser['email'];
            $first_name = $rowUser['first_name'];
            $last_name = $rowUser['last_name'];
            $phone_number = $rowUser['phone_number'];

            // Check if the user has a custom profile picture
            $profilePicture = !empty($rowUser['profile_picture']) ? $rowUser['profile_picture'] : 'https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?size=626&ext=jpg&uid=R124982824&ga=GA1.2.883124554.1703534148&semt=ais';

            // Query to retrieve address information
            $queryAddress = "SELECT * FROM address WHERE user_id = ?";
            $stmtAddress = mysqli_prepare($conn, $queryAddress);
            mysqli_stmt_bind_param($stmtAddress, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($stmtAddress);
            $resultAddress = mysqli_stmt_get_result($stmtAddress);

            if ($resultAddress) {
                // Check if there is address information for the user
                if ($rowAddress = mysqli_fetch_assoc($resultAddress)) {
                    // Extract address information
                    $street_address = $rowAddress['street_address'];
                    $city = $rowAddress['city'];
                    $state = $rowAddress['state'];
                    $postal_code = $rowAddress['postal_code'];
                    $country = $rowAddress['country'];
                } else {
                    // Address not found
                    $errorMessage = "Address not found.";
                }
            } else {
                // Database error for address query
                $errorMessage = "Address query error: " . mysqli_error($conn);
            }
        } else {
            // User not found
            $errorMessage = "User not found.";
        }
    } else {
        // Database error for user query
        $errorMessage = "User query error: " . mysqli_error($conn);
    }
} else {
    // Non-connected user
    $errorMessage = "User not logged in.";
    // You may want to redirect to a login page or handle this case differently
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Add your custom styles here */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            max-width: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h2 {
            margin-bottom: 10px;
        }
      

    </style>
</head>

<body>
<div class="profile-container">
        <div class="profile-info">

            <img src="<?= $profilePicture ?>" alt="Profile Picture" class="profile-image">
            <h2><?= $username ?></h2>
            <p>Email: <?= $email ?></p>
            <p>First Name: <?= $first_name ?></p>
            <p>Last Name: <?= $last_name ?></p>
            <p>Phone Number: <?= $phone_number ?></p>

            <!-- Display address information -->
            <p><strong>Address:</strong></p>
            <p>Street Address: <?= $street_address ?></p>
            <p>City: <?= $city ?></p>
            <p>State: <?= $state ?></p>
            <p>Postal Code: <?= $postal_code ?></p>
            <p>Country: <?= $country ?></p>
        </div>
    </div>
</body>

</html>
