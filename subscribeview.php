<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Newsletter</title>
    <link rel="stylesheet" href="./subscribestyle.css">
</head>

<body>
    <form action="subscribe.php" method="post">
        <h1>Subscribe to Our Newsletter</h1>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Subscribe</button>
        <p id="message"></p>
    </form>

    <script>
        const form = document.querySelector('form');
        const message = document.getElementById('message');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
            });

            const result = await response.text();
            message.textContent = result;
        });
    </script>
</body>

</html>
