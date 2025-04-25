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
    <title>About Us - Milk Market Connect</title>
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
        /* Style for h1 and h2 headings */
        /* Set the text color to dark grey (#333) */
        /* Add 1rem margin below headings for spacing */
        h1, h2 {
            color: #333; /* Dark grey color for headings */
            margin-bottom: 1rem; /* Space below headings */
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
        <h1>About Milk Market Connect</h1>
        <p>Welcome to Milk Market Connect, the premier online platform connecting dairy farmers directly with milk processing plants.</p>

        <h2>Our Mission</h2>
        <p>Our mission is to streamline the milk supply chain, providing a transparent and efficient marketplace for buying and selling raw milk. We aim to empower farmers by giving them direct access to buyers and fair pricing, while helping processing plants source high-quality milk reliably.</p>

        <h2>How It Works</h2>
        <p>Farmers can list their available milk, specifying quantity, quality metrics, and desired price. Processing plants can browse listings, view farmer profiles, and initiate purchases directly through the platform. We facilitate secure communication and transaction management.</p>

        <h2>Why Choose Us?</h2>
        <p>Milk Market Connect offers transparency, efficiency, and fair market access. We are dedicated to supporting the dairy industry by leveraging technology to simplify connections and foster better business relationships between farmers and processing plants.</p>

    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html> 