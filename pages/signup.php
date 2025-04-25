<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Milk Market Connect</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <!-- Auth container: White background, padding, rounded corners, shadow, centered on page -->
    <div class="auth-container">
        <!-- Main heading: Specific color, margin below -->
        <h1>Sign Up for Milk Market Connect</h1>
         <!-- Paragraph: Bottom margin, specific text color -->
        <p>Choose your account type to get started</p>
        
        <!-- Role selection container: Flex layout (centered), gap between items, top/bottom margin, wraps on small screens -->
        <div class="role-selection">
            <!-- Role card link: Light background, border, rounded corners, padding, specific width, transitions on hover -->
            <a href="farmer_signup.php" class="role-card">
                <!-- Role icon: Large font size, margin below -->
                <div class="role-icon">👨‍🌾</div>
                 <!-- Role card heading: Specific font size, margin below, specific text color -->
                <h2>Farmer</h2>
                 <!-- Role card paragraph: Specific font size, no bottom margin -->
                <p>List your milk products and connect with processing plants</p>
            </a>
            
             <!-- Role card link: Light background, border, rounded corners, padding, specific width, transitions on hover -->
            <a href="plant_signup.php" class="role-card">
                 <!-- Role icon: Large font size, margin below -->
                <div class="role-icon">🏭</div>
                <!-- Role card heading: Specific font size, margin below, specific text color -->
                <h2>Processing Plant</h2>
                 <!-- Role card paragraph: Specific font size, no bottom margin -->
                <p>Find milk suppliers and place orders</p>
            </a>
        </div>
        
         <!-- Auth links container: Top margin, smaller font size -->
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
             <!-- Auth link: Specific color, no underline, hover underline effect -->
            <a href="../index.php">Back to Home</a>
        </div>
    </div>
</body>
</html> 