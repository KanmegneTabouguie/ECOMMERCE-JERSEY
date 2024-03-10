<?php
session_start();


include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $raw_password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $registration_date = date("Y-m-d H:i:s"); // Current date and time

    // Hash the password
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Generate a unique verification token
    $verification_token = md5(uniqid(rand(), true));

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO user (username, password, email, first_name, last_name, phone_number, registration_date, verification_token, is_verified, role) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 'user')"; // Default role set to 'user'

    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ssssssss", $username, $hashed_password, $email, $first_name, $last_name, $phone_number, $registration_date, $verification_token);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Registration successful
        echo "Registration successful! Please copy the verification token below and keep it safe.";
        echo "<br>";
        echo "Verification token: $verification_token";

        // Redirect to another page after a delay (40 seconds in this case)
        echo '<script>
                setTimeout(function() {
                    window.location.href = "verification_form.php?email=' . $email . '";
                }, 40000);
              </script>';
    } else {
        // Registration failed
        echo "Error: " . mysqli_stmt_error($stmt);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add User</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="./add_user.css">

</head>

<body>
    <div class="container mt-5">
        <h2>Add User</h2>

        <form action="add_user.php" method="post">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Username:
                </label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email:
                </label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password:
                </label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="form-group">
                <label for="first_name">
                    <i class="fas fa-user"></i> First Name:
                </label>
                <input type="text" class="form-control" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">
                    <i class="fas fa-user"></i> Last Name:
                </label>
                <input type="text" class="form-control" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="phone_number">
                    <i class="fas fa-phone"></i> Phone Number:
                </label>
                <input type="text" class="form-control" name="phone_number" required>
            </div>

            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
