<?php
session_start();

require_once 'database.php';

// Function to get cart items
function getCartItems($conn, $userId) {
    $query = "SELECT cart.cart_id, cart.user_id, cart.product_id, cart.size, cart.quantity, cart.date_added, product.product_name, product.price, product.photo_data
              FROM cart
              INNER JOIN product ON cart.product_id = product.product_id
              WHERE cart.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        displayError('Error preparing statement');
    }

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if result retrieval was successful
    if (!$result) {
        displayError('Error getting result set');
    }

    // Fetch all rows as an associative array
    $cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the statement
    mysqli_stmt_close($stmt);

    return $cartItems;
}

// Function to calculate total price
function calculateTotalPrice($cartItems) {
    $totalPrice = 0;

    foreach ($cartItems as $item) {
        $totalPrice += $item['quantity'] * $item['price'];
    }

    return $totalPrice;
}

// Function to apply promotion code
function applyPromotionCode($conn, $userId, $promotionCode) {
    // Fetch promotion details from the database
    $query = "SELECT * FROM promotions WHERE promotion_code = ? AND DATE(start_date) <= NOW() AND DATE(end_date) >= NOW()";
    $stmt = mysqli_prepare($conn, $query);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        displayError('Error preparing statement');
    }

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "s", $promotionCode);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if result retrieval was successful
    if (!$result) {
        displayError('Error getting result set');
    }

    // Fetch promotion details
    $promotionDetails = mysqli_fetch_assoc($result);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Check if a valid promotion was found
    if ($promotionDetails) {
        // Apply the promotion percentage to the total price
        $cartItems = getCartItems($conn, $userId);
        $totalPrice = calculateTotalPrice($cartItems);
        $discountPercentage = $promotionDetails['discount_percentage'];
        $discountAmount = ($discountPercentage / 100) * $totalPrice;
        $totalPriceAfterDiscount = $totalPrice - $discountAmount;

        // Update the total price in the database or use it as needed
        // For example, you can store it in a session variable for display
        $_SESSION['discounted_total_price'] = $totalPriceAfterDiscount;

        // Return the discounted total price
        return $totalPriceAfterDiscount;
    }

    // Return original total price if no valid promotion is found
    return null;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Establish the database connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Check the connection
if (!$conn) {
    displayError('Error connecting to the database: ' . htmlspecialchars(mysqli_connect_error()));
}

$userId = $_SESSION['user_id'];

// Attempt to get cart items
$cartItems = getCartItems($conn, $userId);

// Check for errors in getCartItems
if ($cartItems === false) {
    displayError('Error getting cart items: ' . htmlspecialchars(mysqli_error($conn)));
}

// Calculate overall total without discount
$overallTotal = calculateTotalPrice($cartItems);

// Check if a promotion code is applied
if (isset($_SESSION['discounted_total_price'])) {
    $overallTotal = $_SESSION['discounted_total_price'];
} else {
    // No discount applied, update overall total in the session
    $_SESSION['overall_total'] = $overallTotal;
}

// Handle updates for both quantity and size
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_details'])) {
    $newSizes = $_POST['new_size'];
    $newQuantities = $_POST['new_quantity'];
    $productIds = $_POST['product_id'];

    // Loop through each product ID to update quantity and size
    foreach ($productIds as $productId) {
        $newSize = $newSizes[$productId];
        $newQuantity = $newQuantities[$productId];

        // Validate the quantity and size (you may want to add additional validation)
        if ($newQuantity >= 0 && in_array($newSize, ['s', 'm', 'l', 'xs', 'xl'])) {
            $updateDetailsQuery = "UPDATE cart SET quantity = ?, size = ? WHERE user_id = ? AND product_id = ?";
            $stmtUpdateDetails = mysqli_prepare($conn, $updateDetailsQuery);

            // Check if the statement was prepared successfully
            if ($stmtUpdateDetails) {
                mysqli_stmt_bind_param($stmtUpdateDetails, "issi", $newQuantity, $newSize, $userId, $productId);
                mysqli_stmt_execute($stmtUpdateDetails);
                mysqli_stmt_close($stmtUpdateDetails);
            }
        }
    }

   // Recalculate overall total after updating cart details
$cartItems = getCartItems($conn, $userId);

// Calculate overall total without discount
$overallTotal = calculateTotalPrice($cartItems);

// Check if a promotion code is applied
if (isset($_SESSION['discounted_total_price'])) {
    // Reapply the discount to the recalculated overall total
    $overallTotal = applyPromotionCode($conn, $userId, $_SESSION['applied_promotion_code']);
    $_SESSION['discounted_total_price'] = $overallTotal;
} else {
    // No discount applied, update overall total in the session
    $_SESSION['overall_total'] = $overallTotal;
}


       
}

// Check if a promotion code is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_promotion'])) {
    $promotionCode = $_POST['promotion_code'];

    // Apply the promotion code and get the discounted total price
    $discountedTotalPrice = applyPromotionCode($conn, $userId, $promotionCode);

    // Check if a valid promotion was applied
    if ($discountedTotalPrice !== null) {
        // You can store or display the discounted total price as needed
        // For example, you can store it in a session variable for display
        $_SESSION['discounted_total_price'] = $discountedTotalPrice;
    } else {
        // Invalid or expired promotion code, handle accordingly
        $_SESSION['discounted_total_price'] = null;
        $_SESSION['promotion_error'] = 'Invalid or expired promotion code.';
    }

    // Recalculate overall total after applying promotion code
    $cartItems = getCartItems($conn, $userId);

    // Calculate overall total without discount
    $overallTotal = calculateTotalPrice($cartItems);

    // Check if a promotion code is applied
    if (isset($_SESSION['discounted_total_price'])) {
        $overallTotal = $_SESSION['discounted_total_price'];
    } else {
        // No discount applied, update overall total in the session
        $_SESSION['overall_total'] = $overallTotal;
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add your custom styles if needed -->
    <link rel="stylesheet" href="./cart.css">
</head>
<body>
<div class="container mt-5">
    <h2>Shopping Cart</h2>

    <?php if ($cartItems !== null): ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <img src="<?= $item['photo_data'] ?>" alt="<?= $item['product_name'] ?>"
                                 style="width: 50px;">
                            <?= $item['product_name'] ?>
                        </td>
                        <td>
                            <select name="new_size[<?= $item['product_id'] ?>]">
                                <option value="s" <?= ($item['size'] === 's') ? 'selected' : '' ?>>S</option>
                                <option value="m" <?= ($item['size'] === 'm') ? 'selected' : '' ?>>M</option>
                                <option value="l" <?= ($item['size'] === 'l') ? 'selected' : '' ?>>L</option>
                                <option value="xs" <?= ($item['size'] === 'xs') ? 'selected' : '' ?>>XS</option>
                                <option value="xl" <?= ($item['size'] === 'xl') ? 'selected' : '' ?>>XL</option>
                                <!-- Add more options as needed -->
                            </select>
                        </td>
                        <td>
                            <input type="number" name="new_quantity[<?= $item['product_id'] ?>]"
                                   value="<?= $item['quantity'] ?>" min="0">
                            <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
                        </td>
                        <td>$<?= $item['price'] ?></td>
                        <td>$<?= $item['quantity'] * $item['price'] ?></td>
                        <td>
                            <button type="submit" name="update_details" class="btn btn-sm btn-primary">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <p class="mt-3"><strong>Overall Total:</strong>
    <?php
    if ($cartItems !== null && !empty($cartItems)) {
        echo '$' . (isset($_SESSION['discounted_total_price']) ? $_SESSION['discounted_total_price'] : $overallTotal);
    } else {
        echo '$0.00';
    }
    ?>
</p>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="mt-3">
            <div class="form-group">
                <label for="promotion_code">Apply Promotion Code:</label>
                <input type="text" name="promotion_code" id="promotion_code" class="form-control"
                       placeholder="Enter code">
            </div>
            <button type="submit" name="apply_promotion" class="btn btn-primary">Apply Promotion</button>
        </form>

    <?php elseif ($cartItems === []): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <p>Failed to retrieve cart items.</p>
    <?php endif; ?>
</div>
<!-- Existing scripts and styles -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>