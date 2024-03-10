<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $verification_token = mysqli_real_escape_string($conn, $_POST['token']);

    // Check if the email and token exist in the database
    $query = "SELECT * FROM user WHERE email = ? AND verification_token = ? AND is_verified = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $verification_token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Email and token are valid
        // Update the user's status to indicate they are verified
        $update_query = "UPDATE user SET is_verified = 1 WHERE email = ? AND verification_token = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ss", $email, $verification_token);
        mysqli_stmt_execute($update_stmt);

        // Close the statements
        mysqli_stmt_close($stmt);
        mysqli_stmt_close($update_stmt);

        // Display success message
        echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Verification Success</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
                <style>
                    body {
                        font-family: "Arial", sans-serif;
                        background-color: #f8f9fa;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 100vh;
                        margin: 0;
                    }

                    .success-container {
                        background-color: #ffffff;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        border-radius: 10px;
                        padding: 30px;
                        text-align: center;
                        width: 100%;
                        max-width: 400px;
                    }

                    h2 {
                        color: #333;
                    }

                    p {
                        color: #555;
                        margin-bottom: 20px;
                    }

                    a {
                        color: #5e72e4;
                        text-decoration: none;
                    }

                    a:hover {
                        text-decoration: underline;
                    }
                </style>
            </head>
            <body>
                <div class="success-container">
                    <h2>Email Verification Successful!</h2>
                    <p>You can now <a href="login.php">login</a>.</p>
                </div>
            </body>
            </html>';
        exit();
    } else {
        // Invalid email or token, or user is already verified
        echo "Invalid verification link. Please make sure you entered the correct email and verification token.";
    }

    // Close the statements
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($update_stmt);
}
?>
