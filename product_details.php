<?php
session_start();

require_once 'database.php';  // Assuming your database connection is in this file

function getProductDetails($conn, $productId) {
    $query = "SELECT * FROM product WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    } else {
        return null;
    }
}

function addToCart($conn, $userId, $productId, $size) {
    $query = "INSERT INTO cart (user_id, product_id, size, quantity, date_added) VALUES (?, ?, ?, 1, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    
    // Ensure that $size is not too long for the 'size' column
    mysqli_stmt_bind_param($stmt, "iis", $userId, $productId, $size);
    
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        die('Error executing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
    return $success;
}


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Use the existing database connection
if (!$conn) {
    die(json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]));
}

$productId = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$productDetails = getProductDetails($conn, $productId);

// Process Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $selectedSize = isset($_POST['size']) ? implode(',', $_POST['size']) : null;

    if ($selectedSize && $productDetails && isset($productDetails['product_id'])) {
        // Add the item to the cart table
        $cartAdditionSuccess = addToCart($conn, $userId, $productId, $selectedSize);

        if ($cartAdditionSuccess) {
            // Redirect to the cart page with success message
            header('Location: cart.php?success=1');
            exit();
        } else {
            // Redirect to the cart page with error message
            header('Location: cart.php?error=1');
            exit();
        }
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* Add your custom styles here */
        body {
            padding-top: 60px; /* Adjust based on your navbar height */
            margin-bottom: 400px; /* Add margin to the bottom to create space for the footer */
        }

        .container {
            margin-top: 40px;
        }

        .product-photo {
            width: 55%;
            float: left;
            margin-right: 5%;
            margin-bottom: 100px; /* Add margin to the bottom to create space for the footer */
        }

        .product-details {
            width: 40%;
            float: left;
        }

        .size-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
        }

        .add-to-cart-button {
            margin-top: 20px;
            margin-bottom: 20px; /* Add margin to the bottom */
        }

        .delivery-info {
            display: flex;
            justify-content: space-between;
            border: 1px solid #ccc;
            padding: 7px;
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .inner-div {
            flex: 1;
            border: 1px solid #ccc;
            padding: 7px;
            text-align: center;
            margin: 10px;
            border-radius: 8px;
            background-color: #fff;
        }

        .inner-div p {
            margin-bottom: 10px;
        }

        /* Adjusted style for the fixed-bottom footer */
        .footer {
            background-color: #f8f9fa; /* Bootstrap background color */
            padding: 20px 0;
            text-align: center;
        }

        .footer p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div>
    <!-- Navbar -->
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
                    <a class="nav-link" href="#">
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
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="fa fa-user"></i> Connexion
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa fa-heart"></i> Favoris
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i> Panier
                    </a>
                </li>
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
                        }
                    ?>
                </li>

            </ul>
        </div>
    </nav>
    <div>
        <br><br/>
    <form method="post">
        <div class="container">
            <?php if ($productDetails): ?>
                <div class="product-photo">
                    <img src="<?= $productDetails['photo_data'] ?>" alt="Product Photo" class="img-fluid">
                </div>
                <div class="product-details">
                    <h2><?= $productDetails['product_name'] ?></h2>
                    <p><strong>Price:</strong> $<?= $productDetails['price'] ?></p>
                    <!-- Display the product description -->
                    <p><strong>Description:</strong> <?= $productDetails['description'] ?></p>
                    <div class="size-box">
                        <p><strong>Available Sizes:</strong></p>
                        <label><input type="checkbox" name="size[]" value="S"> S</label>
                        <label><input type="checkbox" name="size[]" value="M"> M</label>
                        <label><input type="checkbox" name="size[]" value="L"> L</label>
                        <label><input type="checkbox" name="size[]" value="XS"> XS</label>
                        <label><input type="checkbox" name="size[]" value="XL"> XL</label>
                    </div>
                    <button type="submit" class="btn btn-primary add-to-cart-button" name="add_to_cart">Add to Cart</button>
                    <br>
                </div>

                <div class="delivery-info">
                    <div class="inner-div">
                        <p><i class="fa fa-home"></i> Enlèvement en magasin <strong>Gratuit</strong></p>
                        <p>sous 24 heures</p>
                    </div>
                    <div class="inner-div">
                        <p><i class="fa fa-car"></i> Livraison standard à domicile <strong>Gratuit</strong></p>
                        <p>Pour toute commande supérieure à 45 €</p>
                    </div>
                </div>

            <?php else: ?>
                <p>Product not found.</p>
            <?php endif; ?>
        </div>
    </form>

    <footer class="footer fixed-bottom">
        <div class="row">
            <div class="col-md-12">
                <p>Conditions générales d'achat • Politique de confidentialité • Politique de cookies • Mentions légales • Configurer les cookies • SiteMap</p>
                <p>France | Français | © 2024 BERSHKA.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
