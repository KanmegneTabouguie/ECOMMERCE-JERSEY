<?php

require_once __DIR__ . '/database.php';

// Check if Composer dependencies are installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die("Please run 'composer install' to install dependencies.");
}

require_once __DIR__ . '/vendor/autoload.php';

// Set your SendGrid API key (consider using environment variables)
$sendgridApiKey = getenv('SENDGRID_API_KEY');

if (empty($sendgridApiKey)) {
    die("SendGrid API key is missing. Set it as an environment variable.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subscriberEmail = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    if (filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL)) {
        $result = mysqli_query($conn, "SELECT COUNT(*) FROM subscribers WHERE email='$subscriberEmail'");
        $row = mysqli_fetch_row($result);

        if ($row[0] == 0) {
            mysqli_query($conn, "INSERT INTO subscribers (email) VALUES ('$subscriberEmail')");

            $sgEmail = new \SendGrid\Mail\Mail();
            $sgEmail->setFrom("kanmegnea@gmail.com", "Andre");
            $sgEmail->setSubject("Newsletter Subscription Confirmation");
            $sgEmail->addTo($subscriberEmail);
            $sgEmail->addContent("text/plain", "Thank you for subscribing to our newsletter!");

            $sendgrid = new \SendGrid($sendgridApiKey);

            try {
                $response = $sendgrid->send($sgEmail);
               // var_dump($response); // Debugging statement

                echo "Thank you for subscribing! A confirmation email has been sent to your address.";
            } catch (Exception $e) {
                echo "Confirmation email could not be sent. Please try again later.";
            }
        } else {
            echo "You are already subscribed to our newsletter.";
        }
    } else {
        echo "Invalid email address.";
    }
} else {
    header("Location: subscribeview.php");
    exit();
}
?>
