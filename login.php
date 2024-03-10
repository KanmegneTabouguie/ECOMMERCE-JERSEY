<?php
// Start or resume the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./loginstyle.css">
</head>
<body>
    <div class="signup-form">
        <form action="loginProcess.php" method="post" enctype="multipart/form-data">
            <h2>Login</h2>
            
            <?php
            // Display error message if set
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
                // Clear the error message
                unset($_SESSION['login_error']);
            }
            ?>

            <p class="hint-text">Enter Login Details</p>
            <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i>Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required="required">
            </div>
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i>Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="pass" id="password" placeholder="Password" required="required">
                    <div class="input-group-append">
                        <span class="input-group-text" id="togglePassword">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="save" class="btn btn-success btn-lg btn-block">Login</button>
            </div>
            <div class="form-group">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <div class="text-center">Don't have an account? <a href="register.php">Register Here</a></div>
        </form>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        });
    </script>
</body>
</html>
