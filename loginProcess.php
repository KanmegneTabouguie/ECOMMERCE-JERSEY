<?php
// Set session cookie parameters
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0, // Valid until the user closes the browser or logs out
    'path' => $cookieParams['path'],
    'domain' => '', // Set to an empty string or 'localhost' for the local environment
    'secure' => true, // Use 'true' if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Start or resume the session
session_start();

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Max number of login attempts before account lockout
$maxLoginAttempts = 5;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file
    include 'database.php';

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    // Check if the user is locked out
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $maxLoginAttempts) {
        $_SESSION['login_error'] = "Account locked due to too many failed login attempts. Please try again later.";
        header("Location: login.php");
        exit();
    }

    // Query to retrieve user information (including role) for the given email
    $query = "SELECT user_id, username, password, role FROM user WHERE email = ?";
    
    // Use prepared statement
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Check if there is a matching user
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the entered password against the hashed password in the database
            if (password_verify($password, $row['password'])) {
                // Password is correct
                // Set session variables
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Reset login attempts on successful login
                unset($_SESSION['login_attempts']);

                // Redirect based on the user's role
                if ($row['role'] == 'user') {
                    header("Location: home.php");
                } elseif ($row['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    // Handle other roles as needed
                    header("Location: login.php");
                }
                exit();
            } else {
                // Incorrect password
                $_SESSION['login_error'] = "Incorrect email or password";
                
                // Increment login attempts
                $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? ($_SESSION['login_attempts'] + 1) : 1;

                header("Location: login.php");
                exit();
            }
        } else {
            // User not found
            $_SESSION['login_error'] = "Incorrect email or password";
            
            // Increment login attempts
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? ($_SESSION['login_attempts'] + 1) : 1;

            header("Location: login.php");
            exit();
        }
    } else {
        // Database error
        $_SESSION['login_error'] = "Database error: " . mysqli_error($conn);
        header("Location: login.php");
        exit();
    }
} else {
    // Invalid request method
    header("Location: login.php");
    exit();
}
?>
