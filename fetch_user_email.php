<?php
// Include your database connection file
include 'database.php';

// Initialize an array to store the response
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if user_id is provided in the request
    if (isset($_GET['user_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

        // Function to fetch user email based on user_id
        function fetchUserEmail($user_id, $conn)
        {
            $query = "SELECT email FROM user WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $user = mysqli_fetch_assoc($result);
                return $user ? $user['email'] : null;
            } else {
                return null;
            }
        }

        // Fetch user email
        $userEmail = fetchUserEmail($user_id, $conn);

        if ($userEmail) {
            $response['success'] = true;
            $response['email'] = $userEmail;

            // Send email from admin to user
            $adminEmail = 'kanmegnea@gmail.com'; // Replace with your actual admin email
            $subject = 'Reply to Your Contact Form Submission';
            $message = "Dear User,\n\nThank you for your contact form submission. We will get back to you soon.\n\nBest regards,\nAdmin";
            $headers = 'From: ' . $adminEmail;

            // Send email
            $mailSuccess = mail($userEmail, $subject, $message, $headers);

            if (!$mailSuccess) {
                $response['mail_error'] = 'Error sending email to user: ' . error_get_last()['message'];
            }
        } else {
            // No user found with the provided user_id
            $response['success'] = false;
            $response['error'] = 'User not found';
        }
    } else {
        // user_id is not provided in the request
        $response['success'] = false;
        $response['error'] = 'User ID not provided';
    }
}

// Return the response data as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
?>
