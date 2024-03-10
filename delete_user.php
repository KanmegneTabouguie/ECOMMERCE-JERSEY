<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Include the database.php file
    include 'database.php';

    // Get the user ID from the query parameters
    $userId = $_GET['id'];

    // Function to delete a user by user ID
    function deleteUser($userId) {
        global $conn;

        $sql = "DELETE FROM user WHERE user_id = $userId";
        $result = $conn->query($sql);

        if ($result) {
            return true;
        } else {
            // Output the MySQL error for debugging
            echo "Error: " . $conn->error;
            return false;
        }
    }

    // Delete the user from the database
    $deleteResult = deleteUser($userId);

    if ($deleteResult) {
        // Redirect to the members page or display success message
        header("Location: Members.php");
        exit();
    } else {
        // Handle error, e.g., display an error message
        echo "Error deleting user.";
    }
}
?>
