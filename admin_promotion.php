<?php
session_start();


// Include your database connection file (database.php)
include 'database.php';

// Handle form submission to generate a new promotion code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_code'])) {
    // Validate and sanitize input data (you should add more validation as needed)
    $promotionCode = mysqli_real_escape_string($conn, $_POST['promotion_code']);
    $startDate = mysqli_real_escape_string($conn, $_POST['start_date']);
    $endDate = mysqli_real_escape_string($conn, $_POST['end_date']);
    $discountPercentage = mysqli_real_escape_string($conn, $_POST['discount_percentage']);

    // Insert the new promotion code into the database
    $insertQuery = "INSERT INTO promotions (promotion_code, start_date, end_date, discount_percentage) 
                    VALUES ('$promotionCode', '$startDate', '$endDate', '$discountPercentage')";
    mysqli_query($conn, $insertQuery);

    // Optionally, you can display a success message
    $successMessage = "Promotion code generated successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Promotion Page</title>
    <link rel="stylesheet" href="./adminpromotion.css">

    <!-- Include necessary styles/scripts -->
</head>
<body>
<h2>Admin Promotion Page</h2>

<?php
// Display success message if applicable
if (isset($successMessage)) {
    echo "<p>$successMessage</p>";
}
?>

<!-- Form to generate a new promotion code -->
<form action="admin_promotion.php" method="post">
    <label for="promotion_code">Promotion Code:</label>
    <input type="text" name="promotion_code" required>
    <br>
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" required>
    <br>
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" required>
    <br>
    <label for="discount_percentage">Discount Percentage:</label>
    <input type="number" name="discount_percentage" min="0" max="100" required>
    <br>
    <button type="submit" name="generate_code">Generate Promotion Code</button>
</form>

</body>
</html>
