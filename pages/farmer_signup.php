<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    switch ($role) {
        case 'farmer':
            header('Location: farmer/dashboard.php');
            break;
        case 'plant':
            header('Location: plant/dashboard.php');
            break;
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        default:
            header('Location: ../index.php');
    }
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../config/db.php';
    
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
    $contact = mysqli_real_escape_string($conn, $_POST['contact'] ?? '');
    $milk_capacity = floatval($_POST['milk_capacity'] ?? 0);
    $preferred_price = floatval($_POST['preferred_price'] ?? 0);
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($location) || empty($contact) || $milk_capacity <= 0 || $preferred_price <= 0) {
        $error = 'All fields are required and must be valid.';
    } else {
        // Check if email already exists
        $checkEmail = mysqli_query($conn, "SELECT id FROM farmers WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $error = 'Email already exists. Please use a different email or login.';
        } else {
            // Insert new farmer
            $sql = "INSERT INTO farmers (name, email, password, location, contact, milk_capacity, preferred_price) 
                    VALUES ('$name', '$email', '$password', '$location', '$contact', $milk_capacity, $preferred_price)";
            
            if (mysqli_query($conn, $sql)) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Sign Up - Milk Market Connect</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <!-- Auth container: White background, padding, rounded corners, shadow, centered on page -->
    <div class="auth-container">
        <!-- Main heading: Specific color, margin below -->
        <h1>Farmer Sign Up</h1>
        
        <?php if ($error): ?>
            <!-- Error message box: Specific background/text color, padding, margin, rounded corners -->
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
             <!-- Success message box: Specific background/text color, padding, margin, rounded corners -->
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Auth form: Text aligned left -->
        <form method="post" class="auth-form">
            <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="name">Full Name</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="text" id="name" name="name" required value="<?php echo $_POST['name'] ?? ''; ?>">
            </div>
            
             <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="email">Email</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="email" id="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            
             <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="password">Password</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="password" id="password" name="password" required>
            </div>
            
            <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="location">Location</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="text" id="location" name="location" required value="<?php echo $_POST['location'] ?? ''; ?>">
            </div>
            
            <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="contact">Contact Number</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="text" id="contact" name="contact" required value="<?php echo $_POST['contact'] ?? ''; ?>">
            </div>
            
            <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="milk_capacity">Milk Capacity (liters per day)</label>
                <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="number" id="milk_capacity" name="milk_capacity" min="1" step="0.1" required value="<?php echo $_POST['milk_capacity'] ?? ''; ?>">
            </div>
            
            <!-- Form group: Margin below -->
            <div class="form-group">
                <!-- Label: Displayed as block, margin below, medium font weight -->
                <label for="preferred_price">Preferred Price (per liter)</label>
                 <!-- Input field: Full width, padding, border, rounded corners, specific font size, focus style -->
                <input type="number" id="preferred_price" name="preferred_price" min="0.01" step="0.01" required value="<?php echo $_POST['preferred_price'] ?? ''; ?>">
            </div>
            
            <!-- Auth button: Full width, padding, specific background/text color, borderless, rounded corners, specific font size, cursor pointer, top margin, hover effect -->
            <button type="submit" class="btn-auth">Sign Up</button>
        </form>
        
        <!-- Auth links container: Top margin, smaller font size -->
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <!-- Auth link: Specific color, no underline, hover underline effect -->
            <a href="signup.php">Back to Sign Up Options</a>
        </div>
    </div>
</body>
</html> 