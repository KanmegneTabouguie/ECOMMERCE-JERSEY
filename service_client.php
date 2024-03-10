<?php
session_start(); //  session_start to the beginning

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./service_client.css">

</head>

<body>

    <div class="container">
        <h2>Contact Customer Service</h2>

        <?php
        if (isset($_SESSION['user_id'])) {
            // User is authenticated
            echo '<button id="openFormBtn">Open Contact Form</button>';
            echo '<div id="contactForm" style="display:none;">';

            // Display success message if exists
            if (isset($_SESSION['success_message'])) {
                echo '<p style="color: #00e676;">' . $_SESSION['success_message'] . '</p>';
                unset($_SESSION['success_message']); // Clear the session variable
            }

            // Display error message if exists
            if (isset($_SESSION['contact_error'])) {
                echo '<p style="color: #ff5252;">' . $_SESSION['contact_error'] . '</p>';
                unset($_SESSION['contact_error']); // Clear the session variable
            }

            echo '<form id="contactForm" action="handle_service_request.php" method="post">';
            echo '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">';

            echo '<label for="subject">Choose a Subject:</label>';
            echo '<select name="subject">';
            echo '<option value="General Inquiry">General Inquiry</option>';
            echo '<option value="Order Issue">Order Issue</option>';
            echo '<option value="Technical Support">Technical Support</option>';
            // Add more options as needed
            echo '</select>';

            echo '<label for="message">Your Message:</label>';
            echo '<textarea name="message" rows="6" required></textarea>';

            echo '<button type="submit">Submit</button>';
            echo '</form>';
            echo '</div>';
        } else {
            // User is not authenticated
            echo '<p>Please <a href="login.php">login</a> to contact customer service.</p>';
        }
        ?>

    </div>

    <script>
        document.getElementById("openFormBtn").addEventListener("click", function () {
            document.getElementById("contactForm").style.display = "flex";
        });
    </script>

</body>

</html>


