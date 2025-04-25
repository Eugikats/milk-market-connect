<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Milk Market Connect</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Set default font family to Arial or sans-serif for broad compatibility */
        /* Set line height to 1.6 for better readability */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        /* Set the width of the content container to 80% of the viewport */
        /* Set a maximum width of 800px to prevent it from becoming too wide on large screens */
        /* Add 2rem margin to the top and bottom, and auto margin on left/right to center it */
        /* Add 1rem padding inside the container */
        .content-container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto; /* Add some space top/bottom and center horizontally */
            padding: 1rem;
        }
        /* Style for the main heading (h1) */
        /* Set the text color to dark grey (#333) */
        /* Add 1rem margin below the heading for spacing */
        h1 {
            color: #333; /* Dark grey color for the main heading */
            margin-bottom: 1rem; /* Space below the heading */
        }
         /* Style for paragraph text */
        /* Set the text color to a slightly lighter grey (#555) */
        /* Add 1rem margin below paragraphs for spacing */
        p {
             color: #555; /* Slightly lighter grey for paragraph text */
             margin-bottom: 1rem; /* Space below paragraphs */
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <div class="content-container">
        <h1>Privacy Policy</h1>
        <p>Welcome to the Milk Market Connect Privacy Policy.</p>

        <h2>Information We Collect</h2>
        <p>We collect information you provide directly to us, such as when you create an account, list milk, or communicate with us. This may include your name, email address, phone number, location, and details about your milk production or needs.</p>

        <h2>How We Use Information</h2>
        <p>We use the information we collect to operate, maintain, and provide the features and functionality of the Milk Market Connect platform, to communicate with you, and to personalize your experience.</p>

        <h2>Information Sharing</h2>
        <p>We do not share your personal information with third parties except as described in this Privacy Policy or with your consent. We may share information with vendors, consultants, and other service providers who need access to such information to carry out work on our behalf.</p>

        <h2>Your Choices</h2>
        <p>You may update, correct, or delete your account information at any time by logging into your account. If you wish to delete your account, please contact us, but note that we may retain certain information as required by law or for legitimate business purposes.</p>

        <h2>Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>

        <h2>Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact us at privacy@milkmarketconnect.example.com.</p>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html> 