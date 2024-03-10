<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Include necessary files
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
        handleDatabaseError($conn, 'Error preparing statement');
    }

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if result retrieval was successful
    if (!$result) {
        handleDatabaseError($conn, 'Error getting result set');
    }

    // Fetch all rows as an associative array
    $cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the statement
    mysqli_stmt_close($stmt);

    return $cartItems;
}

// Function to process the order
function processOrder($conn, $userId, $cartItems) {
    // Your logic to process the order and update the database
    // For simplicity, let's assume the order is successfully processed
    // You should implement this function based on your requirements

    // Example: Update the order status in the database
    $orderId = saveOrderDetails($conn, $userId, $cartItems);

    return ($orderId !== false);
}

// Function to clear the user's cart
function clearCart($conn, $userId) {
    // Your logic to clear the user's cart in the database
    // For simplicity, let's assume the cart is successfully cleared
    // You should implement this function based on your requirements

    // Example: Delete cart items for the user
    $query = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        handleDatabaseError($conn, 'Error preparing statement to clear cart');
    }
}

// Function to save order details in the database
function saveOrderDetails($conn, $userId, $cartItems) {
    // Example: Insert order details into the orders table
    $query = "INSERT INTO command (user_id, totalprice, order_date, status) VALUES (?, ?, NOW(), 'Pending')";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        $totalPrice = calculateTotalPrice($cartItems);
        mysqli_stmt_bind_param($stmt, "id", $userId, $totalPrice);
        mysqli_stmt_execute($stmt);
        $orderId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // You might also insert individual product details into the same table (orders)
        foreach ($cartItems as $item) {
            $query = "INSERT INTO command (order_id, product_id, quantity, totalprice, size) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiids", $orderId, $item['product_id'], $item['quantity'], $item['totalprice'], $item['size']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                handleDatabaseError($conn, 'Error preparing statement to save order items');
            }
        }

        return $orderId;
    } else {
        handleDatabaseError($conn, 'Error preparing statement to save order details');
    }

    return false;
}

// Function to calculate total price
function calculateTotalPrice($cartItems) {
    $totalPrice = 0;

    foreach ($cartItems as $item) {
        $totalPrice += $item['quantity'] * $item['price'];
    }

    return $totalPrice;
}

// Function to handle database errors
function handleDatabaseError($conn, $errorMessage) {
    // Log the error or display a user-friendly message
    // You might want to redirect the user to an error page
    // For now, we will just echo the error for demonstration purposes
    echo '<p>Error: ' . htmlspecialchars(mysqli_error($conn)) . '</p>';
    exit();
}

// Function to handle payment errors
function handlePaymentError($conn, $errorMessage) {
    // Log the error or display a user-friendly message
    // You might want to redirect the user to an error page
    // For now, we will just echo the error for demonstration purposes
    echo '<p>Error: ' . $errorMessage . '</p>';
    exit();
}


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $cartItems = getCartItems($conn, $userId);

    // Process the order
    $orderSuccess = processOrder($conn, $userId, $cartItems);

    if ($orderSuccess) {
        // Clear the user's cart after a successful order
        clearCart($conn, $userId);

        // Integrate with Stripe for payment processing
        require_once 'vendor/autoload.php'; // Include Stripe PHP library

        \Stripe\Stripe::setApiKey('sk_test_51OlrzmEP2vmEedR8YsuLHItzA6jp5DQ9WkDE637McMMUPigxzCI1zEmnIwIwEr194WUsn6fBeH2MAS0seoF6ylmi00hlJdKdUD');

        try {
            // Create a PaymentIntent
            $intent = \Stripe\PaymentIntent::create([
                'amount' => calculateTotalPrice($cartItems) * 100, // Stripe uses cents, not dollars
                'currency' => 'usd', // Change to your currency
                'payment_method' => $_POST['payment_method_id'],
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            // Handle successful payment
            // You might update your database with payment details or send a confirmation email
            // Redirect to a thank you or order confirmation page
            header('Location: order_confirmation.php');
            exit();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            $errorMessage = 'Failed to process the payment. Please try again.';
            // Log $e->getError()->message for debugging
            handlePaymentError($conn, $errorMessage);
        }
    } else {
        // Handle the case where the order processing fails
        $errorMessage = 'Failed to process the order. Please try again.';
        // You might want to log the error for debugging purposes
        handleOrderProcessingError($conn, $errorMessage);
    }
}

// If the form was not submitted or there was an error, you can redirect back to the cart page
header('Location: cart.php');
exit();
?>
