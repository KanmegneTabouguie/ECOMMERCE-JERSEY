<?php
// Set session cookie parameters
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0, // Valid until the user closes the browser or logs out
    'path' => $cookieParams['path'],
    'domain' => '', // Set to an empty string or 'localhost' for the local environment
    'secure' => false, // Set to false if not using HTTPS in the local environment
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Start session
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Include your database connection file
    include 'database.php';

    // Query to retrieve user information
    $query = "SELECT * FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Check if there is a matching user
        if ($row = mysqli_fetch_assoc($result)) {
            // User information found
            // ... Your code to display user information ...

            // Example: Display username
            $welcomeMessage = "Welcome, " . $row['username'];

        } else {
            // User not found
            $errorMessage = "User not found.";
        }
    } else {
        // Database error
        $errorMessage = "Database error: " . mysqli_error($conn);
    }
} else {
    // Non-connected user
    $welcomeMessage = "Welcome to Finance Portal"; // Customize this message for non-connected users
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <title>Welcome to JERSEY</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./styleshome.css">
</head>

<body>

    <!-- Enhanced Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <!-- Left side of the navbar -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="service_client.php">
                    <i class="fa fa-phone"></i> Service Client
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./subscribeview.php">
                    <i class="fa fa-newspaper-o"></i> Newsletter
                </a>
            </li>
        </ul>

        <!-- Brand logo in the middle -->
        <a class="navbar-brand mx-auto" href="home.php" style="font-family: 'Arial', sans-serif; font-size: 24px; font-weight: bold; color: #fff; text-transform: uppercase;">
            Jersey.Club
        </a>

        <!-- Right side of the navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Profile Icon -->
            <li class="nav-item">
                <?php
                    if (isset($_SESSION['user_id'])) {
                        // Display the profile icon for connected users
                        echo '<a class="nav-link" href="profile.php"><i class="fa fa-user"></i>Profile</a>';
                    }
                ?>
            </li>

            <li class="nav-item">
                <?php
                    if (isset($_SESSION['user_id'])) {
                        // Display the logout button with an icon for connected users
                        echo '<a href="logout.php" class="nav-link"><i class="fa fa-sign-out"></i> Logout</a>';
                    } else {
                        // Display the login button for non-connected users
                        echo '<a href="login.php" class="nav-link"><i class="fa fa-user"></i> Connexion</a>';
                    }
                ?>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./favorites.php">
                    <i class="fa fa-heart"></i> Favoris
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">
                    <i class="fa fa-shopping-cart"></i> Panier
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user_promotion.php">
                    <i class="fas fa-percent"></i> Promotioncode
                </a>
            </li>
        </ul>
    </div>
</nav>

    <!-- Categories beneath the navbar -->
<div class="container mt-3">
    <ul class="nav justify-content-center fixed-top bg-light mt-5">
        <li class="nav-item">
            <a class="nav-link" href="./alluserproduct.php">
                <img src="https://cdn-icons-png.flaticon.com/128/5129/5129670.png" alt="England Flag" width="20" height="20">
                All
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./Premierleague.php">
                <img src="https://cdn-icons-png.flaticon.com/128/4060/4060230.png" alt="England Flag" width="20" height="20">
                Premier League
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./laliga.php">
                <img src="https://cdn-icons-png.flaticon.com/128/13980/13980354.png" alt="Spain Flag" width="20" height="20">
                La Liga
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./bundesliga.php">
                <img src="https://cdn-icons-png.flaticon.com/128/197/197571.png" alt="Germany Flag" width="20" height="20">
                Bundesliga
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./seriea.php">
                <img src="https://cdn-icons-png.flaticon.com/128/323/323325.png" alt="Italy Flag" width="20" height="20">
                Serie A
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./ligue1.php">
                <img src="https://cdn-icons-png.flaticon.com/128/323/323315.png" alt="France Flag" width="20" height="20">
                Ligue 1
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./liganos.php">
                <img src="https://cdn-icons-png.flaticon.com/128/5372/5372974.png" alt="Portugal Flag" width="20" height="20">
                Liga NOS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./eredivise.php">
                <img src="https://cdn-icons-png.flaticon.com/128/323/323275.png" alt="Netherlands Flag" width="20" height="20">
                Eredivisie
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="./pays.php">
                <img src="https://cdn-icons-png.flaticon.com/128/814/814513.png" alt="World Icon" width="20" height="20">
                Pays
            </a>
        </li>
    </ul>
</div>



<div class="responsive-div">
    <h1>Welcome to The <strong>Jersey Club</strong></h1><br>
    <!-- Search Bar -->
    <div class="search-bar">
    <input type="text" placeholder="Discover your perfect jersey...">
    <button type="button"><i class="fas fa-search"></i></button>
</div>

    <div class="banner">
        Est. 2024 - Express your passion for the game with our unique designs. - Happy Moment Every Day
    </div>
</div>


    <div class="jersey-ad">
        <img src="https://63e527a1.flyingcdn.com/wp-content/uploads/2023/11/D390VT3WPKUSFO035W9VJPW12RFKB3UX.jpg" alt="Jersey Photo">
        <div class="jersey-info">
            <div class="jersey-title">Discover Our New Collection</div>
            <div class="jersey-price">Starting at $10.99</div>
            <div class="jersey-price">Pass Ta Command et Reçois ton Maillot gratuit</div>
            <a href="./alluserproduct.php" class="jersey-button">Shop Now</a>
        </div>
    </div>


   



<!-- Footer -->
<footer class="footer fixed-bottom">
            <div class="row">
                <div class="col-md-12">
                    <p>Conditions générales d'achat • Politique de confidentialité • Politique de cookies • Mentions légales • Configurer les cookies • SiteMap</p>
                    <p>France | Français | © 2024 BERSHKA.</p>
                </div>
            </div>
        </div>
    </footer>


    <script>
        // Adjust content padding based on navbar and footer height
        var navbarHeight = $('.navbar').outerHeight();
        var footerHeight = $('.footer').outerHeight();
        $('body').css('padding-top', navbarHeight);
        $('body').css('padding-bottom', footerHeight);
    </script>

</body>

</html>
