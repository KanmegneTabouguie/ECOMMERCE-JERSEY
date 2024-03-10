<?php
session_start();

require_once 'database.php';

// Function to fetch products by IDs
function getProductsByIds($conn, $productIds) {
    if (empty($productIds)) {
        return [];
    }

    $inClause = implode(',', array_map('intval', $productIds));
    $query = "SELECT * FROM product WHERE product_id IN ($inClause)";
    $result = mysqli_query($conn, $query);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Add product details with photo_data as is
        $productDetails = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'price' => $row['price'],
            'photo_data' => $row['photo_data'],
        ];

        $products[] = $productDetails;
    }

    return $products;
}

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the favorites from the cookie
$favorites = isset($_COOKIE['favorites']) ? json_decode($_COOKIE['favorites']) : [];

// Fetch only the favorite products
$favoriteProducts = getProductsByIds($conn, $favorites);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Favorites - Your E-Commerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/your/custom/style.css"> <!-- Add your custom styles here -->
</head>
<body>

    <!-- Navigation Bar -->
    <!-- ... (your navigation bar code remains unchanged) ... -->

    <div class="container mt-3">
        <h2>Your Favorite Products</h2>
        <div class="row">
            <?php foreach ($favoriteProducts as $product) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 product-box">
                        <a href="product_details.php?product_id=<?= $product['product_id'] ?>">
                            <img src="<?= $product['photo_data'] ?>" alt="Product Photo" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?= $product['product_name'] ?></h5>
                                <p class="card-text"><strong>Price:</strong> $<?= $product['price'] ?></p>
                            </div>
                        </a>
                        <!-- Heart icon inside the product box -->
                        <div class="heart-icon product-favorite active" data-product-id="<?= $product['product_id'] ?>">
                            &hearts;
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer fixed-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p>Conditions générales d'achat • Politique de confidentialité • Politique de cookies • Mentions légales • Configurer les cookies • SiteMap</p>
                    <p>France | Français | © 2024 Your E-Commerce Site.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Your additional scripts go here -->

</body>
</html>
