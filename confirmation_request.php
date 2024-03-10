<?php
session_start();

// Check if the success message exists in the session
if (isset($_SESSION['success_message'])) {
    // Display the success message
    echo '<div style="text-align: center; margin-top: 50px;">';
    echo '<h3>' . $_SESSION['success_message'] . '</h3>';
    echo '</div>';

    // Unset the success message to prevent it from displaying again
    unset($_SESSION['success_message']);

    // Redirect to service_client.php after 5 seconds
    echo '<script>
            setTimeout(function(){
                window.location.href = "service_client.php";
            }, 4500);
          </script>';
} else {
    // If there is no success message, redirect to service_client.php immediately
    header("Location: service_client.php");
    exit();
}
?>
