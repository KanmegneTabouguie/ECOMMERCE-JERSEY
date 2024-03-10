<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <title>Register</title>
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;

            // Define the password criteria
            var uppercaseRegex = /[A-Z]/;
            var lowercaseRegex = /[a-z]/;
            var numberRegex = /[0-9]/;
            var specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

            // Check if the password meets the criteria
            var isUppercase = uppercaseRegex.test(password);
            var isLowercase = lowercaseRegex.test(password);
            var isNumber = numberRegex.test(password);
            var isSpecialChar = specialCharRegex.test(password);

            // Check if the password length is at least 8 characters
            var isLengthValid = password.length >= 8;

            // Display feedback to the user
            var feedbackElement = document.getElementById("password-feedback");
            if (isUppercase && isLowercase && isNumber && isSpecialChar && isLengthValid) {
                feedbackElement.innerHTML = "Password strength: Strong";
            } else {
                feedbackElement.innerHTML = "Password strength: Weak. Please include at least one uppercase letter, one lowercase letter, one number, one special character, and have a minimum length of 8 characters.";
            }

               // Check if the password and confirm password match
               var confirmPassword = document.getElementById("confirm-password").value;
            var isPasswordMatch = password === confirmPassword;

            // Display feedback for confirm password
            var confirmFeedbackElement = document.getElementById("confirm-password-feedback");
            if (isPasswordMatch) {
                confirmFeedbackElement.innerHTML = "Password confirmation: Correct";
            } else {
                confirmFeedbackElement.innerHTML = "Password confirmation: Incorrect";
            }
        }

        function validateEmail() {
            var email = document.getElementById("email").value;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Check if the email format is valid
            var isEmailValid = emailRegex.test(email);

            // Display feedback to the user
            var emailFeedbackElement = document.getElementById("email-feedback");
            if (isEmailValid) {
                emailFeedbackElement.innerHTML = "Email format: Valid";
            } else {
                emailFeedbackElement.innerHTML = "Email format: Invalid. Please enter a valid email address.";
            }
        }
    </script>
</head>
<body>
<div class="signup-form">
        <form action="register_a.php" method="post" enctype="multipart/form-data">
        <h2><i class="fa fa-user-circle"></i> Register</h2>
            <p class="hint-text">Create your account</p>
            <div class="form-group">
                <div class="row">
                    <div class="col"><input type="text" class="form-control" name="first_name" placeholder="First Name" required="required"></div>
                    <div class="col"><input type="text" class="form-control" name="last_name" placeholder="Last Name" required="required"></div>
                </div>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username" required="required">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required="required" onkeyup="validateEmail()">
                <div id="email-feedback"></div>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="required" onkeyup="validatePassword()">
                <div id="password-feedback"></div>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required="required" onkeyup="validatePassword()">
                <div id="confirm-password-feedback"></div>
            </div>
            <div class="form-group">
                <input type="tel" class="form-control" name="phone_number" placeholder="Phone Number" required="required">
            </div>
            <div class="form-group">
                <label class="form-check-label"><input type="checkbox" required="required"> I accept the <a href="terms.php">Terms of Use</a> & <a href="privacy.php">Privacy Policy</a></label>
            </div>
            <div class="form-group">
                <button type="submit" name="save" class="btn btn-success btn-lg btn-block">Register Now</button>
            </div>
            <div class="text-center">Already have an account? <a href="login.php">Sign in</a></div>
        </form>
    </div>
</body>
</html>