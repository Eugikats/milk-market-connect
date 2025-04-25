<?php
session_start();

// Redirects if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$role = $_GET['role'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../config/db.php';
    
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');
    
    if (empty($email) || empty($password) || empty($role)) {
        $error = 'All fields are required.';
    } else {
        $table = '';
        switch ($role) {
            case 'farmer':
                $table = 'farmers';
                break;
            case 'plant':
                $table = 'processing_plants';
                break;
            case 'admin':
                $table = 'admins';
                break;
            default:
                $error = 'Invalid role selected.';
        }
        
        if ($table) {
            $sql = "SELECT id, name FROM $table WHERE email = '$email' AND password = '$password'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $role;
                
                // Redirect based on role
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
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
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
    <title>Login - Milk Market Connect</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <!-- Auth container: White background, padding, rounded corners, shadow, centered on page -->
    <div class="auth-container">
         <!-- Main heading: Specific color, margin below -->
        <h1>Login to Milk Market Connect</h1>
        
        <?php if ($error): ?>
            <!-- Error message box: Specific background/text color, padding, margin, rounded corners -->
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
         <!-- Auth form: Text aligned left -->
        <form method="post" class="auth-form">
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
                <label for="role">Login As</label>
                 <!-- Select dropdown (inherits some input styles): Full width, padding, border, rounded corners -->
                <select id="role" name="role" required class="form-control"> <!-- Assuming form-control provides similar styling to input -->
                    <option value="">Select Role</option>
                    <option value="farmer" <?php echo $role === 'farmer' ? 'selected' : ''; ?>>Farmer</option>
                    <option value="plant" <?php echo $role === 'plant' ? 'selected' : ''; ?>>Processing Plant</option>
                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
             <!-- Auth button: Full width, padding, specific background/text color, borderless, rounded corners, specific font size, cursor pointer, top margin, hover effect -->
            <button type="submit" class="btn-auth">Login</button>
        </form>
        
         <!-- Auth links container: Top margin, smaller font size -->
        <div class="auth-links">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
             <!-- Auth link: Specific color, no underline, hover underline effect -->
            <a href="../index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>
