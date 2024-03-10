<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate email (you may want to add more validation)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_error'] = "Invalid email address";
        header("Location: forgot_password.php");
        exit();
    }

    // Check if the email exists in the database
    $query = "SELECT user_id, username FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Update the password in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE user SET password = ? WHERE email = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ss", $hashed_password, $email);
        mysqli_stmt_execute($updateStmt);

        $_SESSION['reset_success'] = "Password reset successfully. You can now log in with your new password.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['reset_error'] = "Email address not found";
        header("Location: forgot_password.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 40px;
        }

        .card-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .label-icon {
            margin-right: 10px;
        }

        input.form-control {
            height: 50px;
            font-size: 16px;
            border-radius: 25px;
        }

        .reveal-icon {
            cursor: pointer;
            color: #555;
        }

        button.btn-success {
            background-color: #5e72e4;
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-success:hover {
            background-color: #4d5bf7;
        }

        .text-danger, .text-success {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Reset Password</h2>
                        <?php
                        if (isset($_SESSION['reset_error'])) {
                            echo '<p class="text-danger">' . $_SESSION['reset_error'] . '</p>';
                            unset($_SESSION['reset_error']);
                        }

                        if (isset($_SESSION['reset_success'])) {
                            echo '<p class="text-success">' . $_SESSION['reset_success'] . '</p>';
                            unset($_SESSION['reset_success']);
                        }
                        ?>
                        <form action="forgot_password.php" method="post">
                            <div class="form-group">
                                <label for="email" class="label-icon"><i class="fas fa-envelope"></i> Email:</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="label-icon"><i class="fas fa-lock"></i> New Password:</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                                <span class="reveal-icon" onclick="togglePassword()"><i class="far fa-eye"></i></span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
