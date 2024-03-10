<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    // Include your database connection file
    include 'database.php';

    $user_id = $_SESSION['user_id']; // Fetch user_id from the session
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Query to insert contact into the contactus table
    $query = "INSERT INTO contactus (user_id, subject, message, contact_date) VALUES (?, ?, ?, NOW())";
    
    // Use prepared statement
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject, $message);
    
    // Execute the statement and check for errors
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Contact successfully inserted
        $_SESSION['success_message'] = "Your request has been submitted successfully. We will get back to you soon.";

        // Redirect to confirmation_request.php
        header("Location: confirmation_request.php");
        exit();
    } else {
        // Log the actual SQL error for debugging
        error_log("SQL Error: " . mysqli_error($conn));

        // Database error
        $_SESSION['contact_error'] = "Oops! Something went wrong. Please try again later.";

        // Redirect back to the form page
        header("Location: service_client.php");
        exit();
    }
} else {
    // Invalid request method or user not authenticated
    header("Location: service_client.php");
    exit();
}
?>
