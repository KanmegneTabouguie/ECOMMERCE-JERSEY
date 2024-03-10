<?php
session_start();

// Include your database connection file (database.php)
include 'database.php';

// Fetch active promotion codes from the database
$todayDate = date("Y-m-d");
$query = "SELECT * FROM promotions WHERE DATE(start_date) <= '$todayDate' AND DATE(end_date) >= '$todayDate'";
$result = mysqli_query($conn, $query);

// Check if there are active promotion codes
if ($result && mysqli_num_rows($result) > 0) {
    $promotionCodes = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $noPromotionMessage = "No active promotion codes available.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Promotion Page</title>
    <!-- Include necessary styles/scripts -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .promotion-container {
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            width: 300px;
        }
        .promotion-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .code-snippet {
            font-family: 'Courier New', monospace;
            background-color: #f0f0f0;
            padding: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .copy-button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="promotion-container">
        <h2>User Promotion Page</h2>

        <?php
        // Display active promotion codes or no message
        if (isset($promotionCodes)) {
            foreach ($promotionCodes as $code) {
                echo "<div class='promotion-box'>
                        <p>Promotion Code: <span class='code-snippet'>{$code['promotion_code']}</span></p>
                        <p>Discount Percentage: {$code['discount_percentage']}%</p>
                        <button class='copy-button' onclick='copyPromotionCode(\"{$code['promotion_code']}\")'>Copy Code</button>
                    </div>";
            }
        } elseif (isset($noPromotionMessage)) {
            echo "<p>$noPromotionMessage</p>";
        }
        ?>

        <script>
            function copyPromotionCode(code) {
                var tempInput = document.createElement('input');
                tempInput.value = code;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('Code copied to clipboard: ' + code);
            }
        </script>
    </div>

    <!-- Add instructions or additional content -->

</body>
</html>
