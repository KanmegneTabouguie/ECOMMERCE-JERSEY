<?php
// Include your database connection file
include 'database.php';

// Initialize an empty array to store contact data
$contacts = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if a specific contact_id is provided in the request
    if (isset($_GET['contact_id'])) {
        $contact_id = mysqli_real_escape_string($conn, $_GET['contact_id']);
        
        // Query to fetch data for a specific contact_id
        $query = "SELECT contact_id, user_id, subject, message, contact_date, reponse, status FROM contactus WHERE contact_id = ?";
        
        // Use prepared statement
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $contact_id);
        
        // Execute the statement
        mysqli_stmt_execute($stmt);
        
        // Fetch the data
        $result = mysqli_stmt_get_result($stmt);
        
        // Check if data is fetched successfully
        if ($result) {
            $contact = mysqli_fetch_assoc($result);
            if ($contact) {
                $contacts[] = $contact;
            } else {
                // No contact found with the provided contact_id
                $contacts['error'] = 'Contact not found';
            }
        } else {
            // Handle the database error
            $contacts['error'] = 'Database error: ' . mysqli_error($conn);
        }
    } else {
        // Fetch all data from the contactus table
        $query = "SELECT contact_id, user_id, subject, message, contact_date, reponse, status FROM contactus";
        
        // Perform the query
        $result = mysqli_query($conn, $query);
        
        // Check for errors
        if ($result) {
            // Fetch all data and store it in the $contacts array
            while ($row = mysqli_fetch_assoc($result)) {
                $contacts[] = $row;
            }
        } else {
            // Handle the database error
            $contacts['error'] = 'Database error: ' . mysqli_error($conn);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required parameters are provided in the request
    if (isset($_POST['contact_id'], $_POST['response'], $_POST['status'])) {
        $contact_id = mysqli_real_escape_string($conn, $_POST['contact_id']);
        $responseValue = mysqli_real_escape_string($conn, $_POST['response']);
        $statusValue = mysqli_real_escape_string($conn, $_POST['status']);

        // Query to update the response and status fields for a specific contact_id
        $updateQuery = "UPDATE contactus SET reponse = ?, status = ? WHERE contact_id = ?";

        // Use prepared statement
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'ssi', $responseValue, $statusValue, $contact_id);

        // Execute the statement
        if (mysqli_stmt_execute($updateStmt)) {
            // Update successful
            $response['success'] = true;
        } else {
            // Handle the database error
            $response['error'] = 'Database error: ' . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($updateStmt);
    } else {
        // Required parameters not provided
        $response['error'] = 'Missing required parameters';
    }
} else {
    // Invalid request method
    $response['error'] = 'Invalid request method';
}

// Return the data or response as JSON
header('Content-Type: application/json');
echo json_encode(!empty($contacts) ? $contacts : $response);

// Close the database connection
mysqli_close($conn);
?>
